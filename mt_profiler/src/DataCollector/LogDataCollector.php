<?php

namespace Concrete\Package\MtProfiler\DataCollector;

use Concrete\Core\Logging\LogList;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;
use Concrete\Package\MtProfiler\DataFormatter\SimpleDataFormatter;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Monolog\Formatter\LineFormatter;

class LogDataCollector extends DataCollector implements Renderable
{
    public function __construct()
    {
        // $this->setDataFormatter(new LineFormatter());
    }

    /**
     * @inheritDoc
     */
    function collect()
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
     * @inheritDoc
     */
    function getName()
    {
        return 'concrete5log';
    }

    /**
     * @inheritDoc
     */
    function getWidgets()
    {
        return [
            "logs" => [
                "icon" => "file-archive-o",
                "widget" => "PhpDebugBar.Widgets.MessagesWidget",
                "map" => "concrete5log.records",
                "default" => "{}"
            ],
            "logs:badge" => array(
                "map" => "concrete5log.count",
                "default" => "null"
            )
        ];
    }

}
