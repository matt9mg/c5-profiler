<?php
declare(strict_types=1);

namespace Concrete\Package\MtProfiler\DataCollector;

use Concrete\Core\Logging\LogList;
use Concrete\Core\User\User;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

/**
 * Class LogDataCollector
 * @package Concrete\Package\MtProfiler\DataCollector
 */
class LogDataCollector extends DataCollector implements Renderable
{
    /**
     * @return array
     */
    function collect(): array
    {
        $list = new LogList();
        $list->filterByTime(strtotime(date('Y-m-d') . ' 00:00:00'), '>=');
        $list->filterByTime(strtotime(date('Y-m-d') . ' 23:59:59'), '<=');
        $logs = $list->get();

        $data = [];
        /**
         * @var \Concrete\Core\Logging\LogEntry $log
         */
        foreach ($logs as $log) {
            $user = $log->getUserObject();
            $username = 'Annoymous user';

            if ($user instanceof User) {
                $username = $user->getUserName();
            }

            $data[] = array(
                'message' => $this->getDataFormatter()->formatVar('[' . $log->getDisplayTimestamp() . '] ' . strtolower($log->getChannelDisplayName()) . '.' . strtoupper($log->getLevelDisplayName()) . ': ' . $log->getMessage() . ' - ' . $username),
                'is_string' => true,
                'label' => strtolower($log->getLevelDisplayName()),
                'time' => $log->getDisplayTimestamp()
            );
        }

        return array(
            'count' => count($data),
            'records' => $data
        );
    }


    /**
     * @return string
     */
    function getName(): string
    {
        return 'concrete5log';
    }

    /**
     * @return \string[][]
     */
    function getWidgets(): array
    {
        return [
            'logs' => [
                'icon' => 'file-archive-o',
                'widget' => 'PhpDebugBar.Widgets.MessagesWidget',
                'map' => 'concrete5log.records',
                'default' => '{}'
            ],
            'logs:badge' => array(
                'map' => 'concrete5log.count',
                'default' => 'null'
            )
        ];
    }

}
