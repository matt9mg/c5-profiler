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

        if (isset($data['uGroups'])) {
            $groups = [];
            foreach ($data['uGroups'] as $groupId) {
                $groups[] = Group::getByID($groupId)->getGroupName();
            }

            $data['uGroups'] = $groups;
        }

        unset($data['dashboardMenus']);

        return $data;
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
                "icon" => "tags",
                "widget" => "PhpDebugBar.Widgets.VariableListWidget",
                "map" => "concrete5session",
                "default" => "{}"
            ]
        ];
    }

}
