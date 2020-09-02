<?php
declare(strict_types=1);

namespace Concrete\Package\MtProfiler\Events;

use Concrete\Package\MtProfiler\Debugbar;
use DebugBar\DataCollector\DataCollectorInterface;
use Symfony\Component\EventDispatcher\Event as AbstractEvent;

/**
 * Class AddDataCollectorEvent
 * @package Concrete\Package\MtProfiler\Events
 */
class AddDataCollectorEvent extends AbstractEvent
{
    public const ADD_DATA_COLLECTOR = 'mt_profiler_add_data_collector';

    private Debugbar $debugbar;

    /**
     * AddDataCollectorEvent constructor.
     * @param Debugbar $debugbar
     */
    public function __construct(Debugbar $debugbar)
    {
        $this->debugbar = $debugbar;
    }

    /**
     * @return Debugbar
     */
    public function getDebugBar(): Debugbar
    {
        return $this->debugbar;
    }

    /**
     * @param DataCollectorInterface $dataCollector
     * @return $this
     * @throws \DebugBar\DebugBarException
     */
    public function addDataCollector(DataCollectorInterface $dataCollector): AddDataCollectorEvent
    {
        $this->debugbar->addCollector($dataCollector);

        return $this;
    }
}
