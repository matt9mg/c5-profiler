<?php
declare(strict_types=1);

namespace Concrete\Package\MtProfiler;

use Concrete\Core\Package\Package;
use Concrete\Core\Routing\Route;
use Concrete\Core\Routing\Router;
use Doctrine\DBAL\Logging\DebugStack;
use MtProfiler\Controller\Api\AssetController;
use MtProfiler\Controller\Api\OpenController;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class MtProfiler
 * @package Concrete\Package\MtProfiler
 */
class Controller extends Package
{
    private const PLACEHOLDER_TEXT = '<!-- debugbar:placeholder -->';

    protected $pkgHandle = 'mt_profiler';
    protected $appVersionRequired = '8.5.1';
    protected $pkgVersion = '0.2';
    protected $pkgAutoloaderRegistries = [
        'src'=>'\Concrete\Package\MtProfiler',
    ];

    /**
     * @return string
     */
    public function getPackageDescription(): string
    {
        return t('Adds the MT Profiler to your website.');
    }

    /**
     * @return string
     */
    public function getPackageName(): string
    {
        return t('MT Profiler');
    }

    public function on_start(): void
    {
        $doctrineDebugStack = new DebugStack();
        $this->app->make('Concrete\Core\Database\DatabaseManager')->getConfiguration()->setSQLLogger($doctrineDebugStack);

        /**
         * @var Router $router
         */
        $router = $this->app->make(Router::class);
        $route = new Route('/mt_profiler/open/');
        $route->setAction(OpenController::class . '::handle');

        $router->addRoute($route);

        $route = new Route('/mt_profiler/assets/css/');
        $route->setAction(AssetController::class . '::css');

        $router->addRoute($route);

        $route = new Route('/mt_profiler/assets/js/');
        $route->setAction(AssetController::class . '::js');

        $router->addRoute($route);

        require_once __DIR__ . '/vendor/autoload.php';

        $app = $this->getApplication();

        $app->singleton('debugbar', Debugbar::class);
        $app->bind('debugbar/renderer', function () use ($app) {
            /** @var Debugbar $debugbar */
            $debugbar = $app->make('debugbar');
            return $debugbar->getJavascriptRenderer($this->getRelativePath().'/vendor/maximebf/debugbar/src/DebugBar/Resources');
        });
        $app->bind('debugbar/messages', function () use ($app) {
            $debugbar = $app->make('debugbar');
            return $debugbar['messages'];
        });
        $app->bind('debugbar/time', function () use ($app) {
            $debugbar = $app->make('debugbar');
            return $debugbar['time'];
        });
        $app->make('director')->addListener('on_before_dispatch', function () use ($app){
            $app->make('debugbar');
        });

        $app->make('director')->addListener('on_before_render', function ($event) use ($app) {
            $debugbarRenderer = $app->make('debugbar/renderer');
            $v = $event->getArgument('view');
            $v->addHeaderItem($debugbarRenderer->renderHead());
            $v->addFooterItem(self::PLACEHOLDER_TEXT);
        });

        $app->make('director')->addListener('on_page_output', function ($event) use ($app) {
            $debugbarRenderer = $app->make('debugbar/renderer');
            $contents = $event->getArgument('contents');
            $contents = str_replace(self::PLACEHOLDER_TEXT, $debugbarRenderer->render(), $contents);
            $event->setArgument('contents', $contents);
        });
    }

    public function onTestEvent() {
        sleep(2);
    }

    public function onTestEvent2() {
        sleep(6);
    }

    public function onTestEvent3() {
        sleep(1);
    }
}
