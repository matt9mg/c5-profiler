<?php
declare(strict_types=1);

namespace Concrete\Package\MtProfiler;

use Concrete\Core\Database\DatabaseManager;
use Concrete\Core\Package\Package;
use Concrete\Core\Routing\Route;
use Concrete\Core\Routing\Router;
use Concrete\Package\MtProfiler\DependencyInjection\Container;
use Concrete\Package\MtProfiler\Events\Listeners;
use Concrete\Package\MtProfiler\Router\Routes;
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

            (new Routes($this->app->make(Router::class)))->register();
            (new Container($this->app))->register($this->getRelativePath());
            (new Listeners($this->app))->listen();
        }
    }
}
