<?php
declare(strict_types=1);

namespace Concrete\Package\MtProfiler\Router;

use Concrete\Core\Routing\Route;
use Concrete\Core\Routing\Router;
use MtProfiler\Controller\Api\AssetController;
use MtProfiler\Controller\Api\OpenController;

/**
 * Class Routes
 * @package Concrete\Package\MtProfiler\Router
 */
class Routes
{
    private Router $router;

    /**
     * Routes constructor.
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Register our custom routes
     */
    public function register(): void
    {
        $route = new Route('/mt_profiler/open/');
        $route->setAction(OpenController::class . '::handle');

        $this->router->addRoute($route);

        $route = new Route('/mt_profiler/assets/css/');
        $route->setAction(AssetController::class . '::css');

        $this->router->addRoute($route);

        $route = new Route('/mt_profiler/assets/js/');
        $route->setAction(AssetController::class . '::js');

        $this->router->addRoute($route);
    }
}
