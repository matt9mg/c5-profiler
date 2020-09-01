<?php
declare(strict_types=1);

namespace Concrete\Package\MtProfiler;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\DatabaseManager;
use Concrete\Core\Http\Request;
use Concrete\Core\Routing\Router;
use Concrete\Package\MtProfiler\DataCollector\BlockDataCollector;
use Concrete\Package\MtProfiler\DataCollector\DoctrineDataCollector;
use Concrete\Package\MtProfiler\DataCollector\EnvironmentDataCollector;
use Concrete\Package\MtProfiler\DataCollector\EventDataCollector;
use Concrete\Package\MtProfiler\DataCollector\LogDataCollector;
use Concrete\Package\MtProfiler\DataCollector\MailDataCollector;
use Concrete\Package\MtProfiler\DataCollector\MonologDataCollector;
use Concrete\Package\MtProfiler\DataCollector\RequestDataCollector;
use Concrete\Package\MtProfiler\DataCollector\RouteDataCollector;
use Concrete\Package\MtProfiler\DataCollector\SessionDataCollector;
use Concrete\Package\MtProfiler\DataCollector\UserDataCollector;
use Concrete\Package\MtProfiler\Renderer\JsRenderer;
use DebugBar\DataCollector\ConfigCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\Storage\FileStorage;


/**
 * Class Debugbar
 * @package Concrete\Package\MtProfiler
 */
class Debugbar extends \DebugBar\DebugBar
{
    /**
     * Debugbar constructor.
     */
    public function __construct(Application $app)
    {
        @mkdir(DIR_BASE . REL_DIR_FILES_UPLOADED_STANDARD . '/storage');
        $this->setStorage(new FileStorage(DIR_BASE . REL_DIR_FILES_UPLOADED_STANDARD . '/storage'));
        $renderer = $this->getJavascriptRenderer('/packages/mt_profiler/vendor/maximebf/debugbar/src/DebugBar/Resources');
        $renderer->setOpenHandlerUrl('/mt_profiler/open/');

        if ($app->make('config')->get('mt_profiler.profilers.php_info') === true) {
            $this->addCollector(new PhpInfoCollector());
        }

        if ($app->make('config')->get('mt_profiler.profilers.messages') === true) {
            $this->addCollector(new MessagesCollector());
        }
        $this->addCollector(new TimeDataCollector());
        $this->addCollector(new MemoryCollector());
        $this->addCollector(new RequestDataCollector());
        $this->addCollector(new SessionDataCollector());
        $this->addCollector(new MonologDataCollector($app->make('log')));


        $logger = $app->make(DatabaseManager::class)->getConfiguration()->getSqlLogger();
        $this->addCollector(new DoctrineDataCollector($logger));
        $this->addCollector(new LogDataCollector());
        $this->addCollector(new EnvironmentDataCollector());


        $startTime = Request::getInstance()->server->get('REQUEST_TIME_FLOAT');
        $eventCollector = new EventDataCollector($startTime);
        $eventCollector->subscribe($app->make('director'));
        $this->addCollector($eventCollector);

        $configCollector = new ConfigCollector();
        $configCollector->setData($app->make('config')->all());
        $configCollector->useHtmlVarDumper(true);
        $this->addCollector($configCollector);

        $routeCollector = new RouteDataCollector();
        $this->addCollector($routeCollector);

        $userCollector = new UserDataCollector();
        $this->addCollector($userCollector);

        $blockCollector = new BlockDataCollector();
        $blockCollector->subscribe($app->make('director'));
        $this->addCollector($blockCollector);

        $mailCollector = new MailDataCollector();
        $this->addCollector($mailCollector);
    }

    /**
     * Returns a JavascriptRenderer for this instance
     *
     * @param string $baseUrl
     * @param string $basePathng
     * @return JsRenderer
     */
    public function getJavascriptRenderer($baseUrl = null, $basePath = null)
    {
        if ($this->jsRenderer === null) {
            $this->jsRenderer = new JsRenderer($this, $baseUrl, $basePath);
        }

        return $this->jsRenderer;
    }
}
