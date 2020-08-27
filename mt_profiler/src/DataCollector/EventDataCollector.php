<?php


namespace Concrete\Package\MtProfiler\DataCollector;

use Concrete\Package\MtProfiler\DataFormatter\SimpleDataFormatter;
use DebugBar\DataCollector\TimeDataCollector;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Str;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventDataCollector extends TimeDataCollector
{
    /** @var EventDispatcher */
    protected $events;

    /** @var integer */
    protected $previousTime;

    public function __construct($requestStartTime = null)
    {
        parent::__construct($requestStartTime);
        $this->previousTime = microtime(true);
        $this->setDataFormatter(new SimpleDataFormatter());
    }

    public function onWildcardEvent($event = null, $name = null)
    {
        $currentTime = microtime(true);

        // Find all listeners for the current event
        foreach ($this->events->getListeners($name) as $i => $listener) {
            // Check if it's an object + method name
            if (is_array($listener) && count($listener) > 1 && is_object($listener[0])) {
                list($class, $method) = $listener;

                // Skip this class itself
                if ($class instanceof static) {
                    continue;
                }

                // Format the listener to readable format
                $listener = get_class($class) . '@' . $method;

                // Handle closures
            } elseif ($listener instanceof \Closure) {
                $reflector = new \ReflectionFunction($listener);

                // Format the closure to a readable format
                $filename = ltrim(str_replace(REL_DIR_APPLICATION, '', $reflector->getFileName()), '/');
                $lines = $reflector->getStartLine() . '-' . $reflector->getEndLine();
                $listener = $reflector->getName() . ' (' . $filename . ':' . $lines . ')';
            } else {
                // Not sure if this is possible, but to prevent edge cases
                $listener = $this->getDataFormatter()->formatVar($listener);
            }

            $params['listeners.' . $i] = $listener;
        }

        $this->addMeasure($name, $this->previousTime, $currentTime, $params);
        $this->previousTime = $currentTime;
    }

    public function subscribe(EventDispatcher $events)
    {
        $this->events = $events;

        foreach ($this->events->getListeners() as $index => $closer) {
            $this->events->addListener($index, [$this, 'onWildcardEvent'], 9999999);
        }
    }

    public function collect()
    {
        $data = parent::collect();
        $data['nb_measures'] = count($data['measures']);

        return $data;
    }

    public function getName()
    {
        return 'event';
    }

    public function getWidgets()
    {
        return [
            "events" => [
                "icon" => "tasks",
                "widget" => "PhpDebugBar.Widgets.TimelineWidget",
                "map" => "event",
                "default" => "{}",
            ],
            'events:badge' => [
                'map' => 'event.nb_measures',
                'default' => 0,
            ],
        ];
    }
}
