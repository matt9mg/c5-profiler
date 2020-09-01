<?php
declare(strict_types=1);

namespace Concrete\Package\MtProfiler;

use Concrete\Core\Database\DatabaseManager;
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

    protected string $pkgHandle = 'mt_profiler';
    protected $appVersionRequired = '8.5.1';
    protected string $pkgVersion = '0.2';
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
        require_once __DIR__ . '/vendor/autoload.php';

        $config = $this->app->make('config')->get('mt_profiler');

        if (isset($config['active']) === false || $config['active'] === true) {
            $this->app->make(DatabaseManager::class)->getConfiguration()->setSQLLogger(new DebugStack());

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

            $app = $this->getApplication();

            $app->singleton('debugbar', Debugbar::class);
            $app->bind('debugbar/renderer', function () use ($app) {
                return $app->make('debugbar')->getJavascriptRenderer($this->getRelativePath().'/vendor/maximebf/debugbar/src/DebugBar/Resources');
            });
            $app->bind('debugbar/messages', static function () use ($app) {
                return $app->make('debugbar')['messages'];
            });
            $app->bind('debugbar/time', static function () use ($app) {
                return $app->make('debugbar')['time'];
            });
            $app->make('director')->addListener('on_before_dispatch', static function () use ($app){
                $app->make('debugbar');
            });

            $app->make('director')->addListener('on_before_render', static function ($event) use ($app) {
                $v = $event->getArgument('view');
                $v->addHeaderItem($app->make('debugbar/renderer')->renderHead());
                $v->addFooterItem(self::PLACEHOLDER_TEXT);
            });

            $app->make('director')->addListener('on_page_output', static function ($event) use ($app) {
                $contents = $event->getArgument('contents');
                $contents = str_replace(self::PLACEHOLDER_TEXT, $app->make('debugbar/renderer')->render(), $contents);
                $event->setArgument('contents', $contents);
            });
        }
    }
}
