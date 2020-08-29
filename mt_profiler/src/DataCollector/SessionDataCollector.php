<?php
namespace Concrete\Package\MtProfiler\DataCollector;

use Concrete\Core\User\Group\Group;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Session;

class SessionDataCollector extends DataCollector implements Renderable
{
    /**
     * @inheritDoc
     */
    function collect()
    {
        $data = Session::all();
        $items = [];

        foreach ($data as $key => $datum) {
            $items[$key] = $this->getVarDumper()->renderVar($datum);
        }

        return $items;
    }

    /**
     * @inheritDoc
     */
    function getName()
    {
        return 'concrete5session';
    }

    /**
     * @inheritDoc
     */
    function getWidgets()
    {
        return [
            "session" => [
                "icon" => "history",
                "widget" => "PhpDebugBar.Widgets.HtmlVariableListWidget",
                "map" => "concrete5session",
                "default" => "{}"
            ]
        ];
    }

}
