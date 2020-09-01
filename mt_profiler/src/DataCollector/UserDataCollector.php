<?php
declare(strict_types=1);
namespace Concrete\Package\MtProfiler\DataCollector;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\User;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

/**
 * Class UserDataCollector
 * @package Concrete\Package\MtProfiler\DataCollector
 */
class UserDataCollector extends DataCollector implements Renderable
{
    /**
     * @return array
     */
    function collect(): array
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

            $data['User info'] = $this->getVarDumper()->renderVar($user->getUserInfoObject());
            $data['Action'] = '<a class=\'\' href=' . $app->make('url/manager')->resolve(['/login', 'do_logout', $app->make('token')->generate('do_logout')]) . '>Logout</a>';
        }

        return $data;
    }

    /**
     * @return string
     */
    function getName(): string
    {
        return 'user';
    }

    /**
     * @return array|\string[][]
     */
    function getWidgets(): array
    {
        return [
            'user' => [
                'icon' => 'user',
                'widget' => 'PhpDebugBar.Widgets.HtmlVariableListWidget',
                'map' => 'user',
                'default' => '{}'
            ]
        ];
    }

}
