<?php
declare(strict_types=1);

namespace Concrete\Package\MtProfiler\DependencyInjection;

use Concrete\Core\Application\Application;
use Concrete\Core\Routing\Router;
use Concrete\Package\MtProfiler\Debugbar;
use Concrete\Package\MtProfiler\Events\Listeners;
use Concrete\Package\MtProfiler\Router\Routes;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Container
 * @package Concrete\Package\MtProfiler\DependencyInjection
 */
class Container
{
    private Application $app;

    /**
     * Container constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Register our services
     * @param string $pgkRelativePath
     */
    public function register(string $pgkRelativePath): void
    {
        $this->app->singleton(Debugbar::class, static function (Application $app) {
            return new DebugBar($app);
        });

        $this->app->bind('debugbar/renderer', function () use ($pgkRelativePath) {
            return $this->app->make(Debugbar::class)->getJavascriptRenderer($pgkRelativePath . '/vendor/maximebf/debugbar/src/DebugBar/Resources');
        });

        $this->app->bind('debugbar/messages', function () {
            return $this->app->make(Debugbar::class)['messages'];
        });

        $this->app->bind('debugbar/time', function () {
            return $this->app->make(Debugbar::class)['time'];
        });
    }
}
