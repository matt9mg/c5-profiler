<?php

namespace Concrete\Package\MtProfiler\DataCollector;

use Concrete\Core\Page\Controller\AccountPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\View\PageView;
use Concrete\Core\Routing\MatchedRoute;
use Concrete\Core\Routing\Router;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

use Illuminate\Support\Facades\Config;

/**
 * Class RouteDataCollectorCollector
 * @package Barryvdh\Debugbar\DataCollector
 */
class RouteDataCollector extends DataCollector implements Renderable
{
    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        return $this->getRouteInformation();
    }

    /**
     * Get the route information for a given route.
     *
     * @return array
     */
    protected function getRouteInformation()
    {
        $request = \Concrete\Core\Http\Request::getInstance();

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
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'route';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        $widgets = [
            "route" => [
                "icon" => "share",
                "widget" => "PhpDebugBar.Widgets.VariableListWidget",
                "map" => "route",
                "default" => "{}"
            ]
        ];

        $widgets['currentroute'] = [
            "icon" => "share",
            "tooltip" => "Route",
            "map" => "route.uri",
            "default" => ""
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
