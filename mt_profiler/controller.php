<?php
declare(strict_types=1);

namespace Concrete\Package\MtProfiler;

use Concrete\Core\Cache\CacheClearer;
use Concrete\Core\Database\DatabaseManager;
use Concrete\Core\Package\Package;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Core\Routing\Router;
use Concrete\Package\MtProfiler\DependencyInjection\Container;
use Concrete\Package\MtProfiler\Events\Listeners;
use Concrete\Package\MtProfiler\Router\Routes;
use Doctrine\DBAL\Logging\DebugStack;

/**
 * Class MtProfiler
 * @package Concrete\Package\MtProfiler
 */
class Controller extends Package
{
    protected string $pkgHandle = 'mt_profiler';
    protected $appVersionRequired = '8.5.1';
    protected string $pkgVersion = '0.2';
    protected $pkgAutoloaderRegistries = [
        'src' => '\Concrete\Package\MtProfiler',
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

    public function on_start()
    {
        require_once __DIR__ . '/vendor/autoload.php';
        $config = $this->app->make('config')->get('mt_profiler');

        if ($config['active'] === true) {
            $this->app->make(DatabaseManager::class)->getConfiguration()->setSQLLogger(new DebugStack());

            (new Routes($this->app->make(Router::class)))->register();
            (new Container($this->app))->register($this->getRelativePath());
            (new Listeners($this->app))->listen();
        }
    }


    public function install()
    {
        // for some very of reason we need to require the autoloader if this has already been installed before :/
        $this->on_start();

        $pkg = parent::install();

        $singlePageObject = Page::getByPath('/dashboard/mt_profiler');
        // Check if it exists, if not, add it
        if ((!is_object($singlePageObject)) || $singlePageObject->isError()) {
            $sp = SinglePage::add('/dashboard/mt_profiler', $pkg);

            // And make sure we update the page with the remaining values
            $sp->update([
                'cName' => 'MT Profiler',
                'excludeFromNav' => false,
            ]);
            $sp->setAttribute('exclude_nav', false);
        }
    }

    public function uninstall()
    {
        $this->app->make(DatabaseManager::class)->getConfiguration()->setSQLLogger(null);

        parent::uninstall();

    }
}
