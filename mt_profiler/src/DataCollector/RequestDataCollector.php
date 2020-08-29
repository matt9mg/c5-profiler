<?php
namespace Concrete\Package\MtProfiler\DataCollector;

use Concrete\Core\Http\Request;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

class RequestDataCollector extends DataCollector implements Renderable
{
    /**
     * @inheritDoc
     */
    function collect()
    {
        /** @var Request $request */
        $request = \Core::make('Concrete\Core\Http\Request');

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
     * @inheritDoc
     */
    function getName()
    {
        return 'concrete5request';
    }

    /**
     * @inheritDoc
     */
    function getWidgets()
    {
        return [
            "request" => [
                "icon" => "thumbs-up",
                "widget" => "PhpDebugBar.Widgets.HtmlVariableListWidget",
                "map" => "concrete5request",
                "default" => "{}"
            ]
        ];
    }

}
