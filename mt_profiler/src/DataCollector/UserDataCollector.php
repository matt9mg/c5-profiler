<?php
namespace Concrete\Package\MtProfiler\DataCollector;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\User;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

class UserDataCollector extends DataCollector implements Renderable
{
    /**
     * @inheritDoc
     */
    function collect()
    {
        $app = Application::getFacadeApplication();
        $user = $app->make(User::class);


        $data['Logged in as'] = $user->getUserID() ? $user->getUserName() : 'Annoymous User';
        $data['Authenticated'] = $user->getUserID() ? true : false;

        if ($data['Authenticated'] === true) {
            $groupIds = $user->getUserGroups();

            $memberOf = [];
            /**
             * @var Group $group
             */
            foreach($groupIds as $groupId) {

                $group = Group::getByID($groupId);
                $memberOf[] = $group->getGroupName();
            }

            $data['Groups'] = implode(', ', $memberOf);
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    function getName()
    {
        return 'user';
    }

    /**
     * @inheritDoc
     */
    function getWidgets()
    {
        return [
            "user" => [
                "icon" => "user",
                "widget" => "PhpDebugBar.Widgets.VariableListWidget",
                "map" => "user",
                "default" => "{}"
            ]
        ];
    }

}
