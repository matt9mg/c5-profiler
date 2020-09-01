<?php
declare(strict_types=1);

namespace Concrete\Package\MtProfiler\DataCollector;

use Concrete\Core\Http\Request;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

/**
 * Class RouteDataCollectorCollector
 * @package Barryvdh\Debugbar\DataCollector
 */
class RouteDataCollector extends DataCollector implements Renderable
{
    /**
     * @return array|string[]
     */
    public function collect(): array
    {
        return $this->getRouteInformation();
    }

    /**
     * @return string[]
     * @throws \ReflectionException
     */
    protected function getRouteInformation(): array
    {
        $request = Request::getInstance();

        $uri = $request->getMethod() . ' ' . $request->getRequestUri();

        $result = [
            'uri' => $uri ?: '-',
        ];

        $controller = $request->getCurrentPage()->getPageController();

        if (isset($controller)) {
            $method = $controller->getAction();
            $class = get_class($controller);

            if (class_exists($class) === true && method_exists($class, $method) === true) {
                $reflector = new \ReflectionMethod($class, $method);

                $result['controller'] = $class . '@' . $method;
            }

            if (isset($reflector)) {
                $filename = ltrim(str_replace(REL_DIR_APPLICATION, '', $reflector->getFileName()), '/');
                $result['controller file'] = $filename . ':' . $reflector->getStartLine() . '-' . $reflector->getEndLine();
            }

            $result['view'] = $controller->getThemeViewTemplate();
        }


        return $result;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'route';
    }

    /**
     * @return \string[][]
     */
    public function getWidgets(): array
    {
        $widgets = [
            'route' => [
                'icon' => 'share',
                'widget' => 'PhpDebugBar.Widgets.VariableListWidget',
                'map' => 'route',
                'default' => '{}'
            ]
        ];

        $widgets['currentroute'] = [
            'icon' => 'share',
            'tooltip' => 'Route',
            'map' => 'route.uri',
            'default' => ''
        ];

        return $widgets;
    }

    /**
     * Display the route information on the console.
     *
     * @param array $routes
     * @return void
     */
    protected function displayRoutes(array $routes)
    {
        $this->table->setHeaders($this->headers)->setRows($routes);

        $this->table->render($this->getOutput());
    }
}
