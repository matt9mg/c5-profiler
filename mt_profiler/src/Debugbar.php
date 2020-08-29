<?php
namespace Concrete\Package\MtProfiler;


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


class Debugbar extends \DebugBar\DebugBar
{
    /**
     * Debugbar constructor.
     */
    public function __construct()
    {
        @mkdir(DIR_BASE . REL_DIR_FILES_UPLOADED_STANDARD . '/storage');
        $this->setStorage(new FileStorage(DIR_BASE . REL_DIR_FILES_UPLOADED_STANDARD . '/storage'));
        $renderer = $this->getJavascriptRenderer('/packages/mt_profiler/vendor/maximebf/debugbar/src/DebugBar/Resources');
        $renderer->setOpenHandlerUrl('/mt_profiler/open/');

        $this->addCollector(new PhpInfoCollector());
        $this->addCollector(new MessagesCollector());$this->addCollector(new TimeDataCollector());
        $this->addCollector(new MemoryCollector());
        $this->addCollector(new RequestDataCollector());
        $this->addCollector(new SessionDataCollector());

        $app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
        $this->addCollector(new MonologDataCollector($app->make('log')));


        $logger = $app->make('Concrete\Core\Database\DatabaseManager')->getConfiguration()->getSqlLogger();
        $this->addCollector(new DoctrineDataCollector($logger));
        $this->addCollector(new LogDataCollector());
        $this->addCollector(new EnvironmentDataCollector());


        $startTime = Request::getInstance()->server->get('REQUEST_TIME_FLOAT');
        $eventCollector = new EventDataCollector($startTime);
        $this->addCollector($eventCollector);
        $eventCollector->subscribe($app->make('director'));

        $configCollector = new ConfigCollector();
        $configCollector->setData($app->make('config')->all());
        $configCollector->useHtmlVarDumper(true);
        $this->addCollector($configCollector);

        $routeCollector = new RouteDataCollector($app->make(Router::class));
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
