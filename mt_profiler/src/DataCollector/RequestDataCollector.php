<?php
declare(strict_types=1);

namespace Concrete\Package\MtProfiler\DataCollector;

use Concrete\Core\Http\Request;
use DebugBar\DataCollector\AssetProvider;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

/**
 * Class RequestDataCollector
 * @package Concrete\Package\MtProfiler\DataCollector
 */
class RequestDataCollector extends DataCollector implements Renderable, AssetProvider
{
    /**
     * @return array
     */
    function collect(): array
    {
        $request = Request::getInstance();

        $data = [];
        $data['path'] = $this->getVarDumper()->renderVar($request->getPath());
        $data['query'] = $this->getVarDumper()->renderVar($request->query);
        $data['cookies'] = $this->getVarDumper()->renderVar($request->cookies);
        $data['headers'] = $this->getVarDumper()->renderVar($request->headers);
        $data['host'] = $this->getVarDumper()->renderVar($request->getHost());
        $data['port'] = $this->getVarDumper()->renderVar($request->getPort());
        $data['clientip'] = $this->getVarDumper()->renderVar($request->getClientIp());

        return $data;
    }

    /**
     * @return string
     */
    function getName(): string
    {
        return 'concrete5request';
    }

    /**
     * @return \string[][]
     */
    function getWidgets(): array
    {
        return [
            'request' => [
                'icon' => 'thumbs-up',
                'widget' => 'PhpDebugBar.Widgets.HtmlVariableListWidget',
                'map' => 'concrete5request',
                'default' => '{}'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getAssets(): array
    {
        return $this->getVarDumper()->getAssets();
    }

}
