<?php


namespace Concrete\Package\MtProfiler\DataCollector;

use Concrete\Core\Block\Block;
use Concrete\Core\Logging\LogList;
use Concrete\Package\MtProfiler\DataFormatter\SimpleDataFormatter;
use DebugBar\DataCollector\AssetProvider;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use DebugBar\DataCollector\TimeDataCollector;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Str;
use Symfony\Component\EventDispatcher\EventDispatcher;

class MailDataCollector extends DataCollector implements Renderable, AssetProvider
{
    public function collect()
    {
        $list = new LogList();
        $list->filterByTime(strtotime(date('Y-m-d') . ' 00:00:00'), '>=');
        $list->filterByTime(strtotime(date('Y-m-d') . ' 23:59:59'), '<=');
        $list->filterByChannel('sent_emails');

        $mails = $list->get();

        $data = [];

        /**
         * @var \Concrete\Core\Logging\LogEntry $mail
         */
        foreach ($mails as $mail) {
            $data[$mail->getDisplayTimestamp()] = [
              $this->getVarDumper()->renderVar(['Mail' => $mail->getMessage()])
            ];
        }

        return [
            'records' => $data,
            'count' => \count($data)
        ];
    }

    public function getName()
    {
        return 'mail';
    }

    public function getWidgets()
    {
        return [
            "mail" => [
                "icon" => "inbox",
                "widget" => "PhpDebugBar.Widgets.HtmlVariableListWidget",
                "map" => "mail.records",
                "default" => "{}"
            ],
            "mail:badge" => array(
                "map" => "mail.count",
                "default" => "null"
            )
        ];
    }

    /**
     * @return array
     */
    public function getAssets()
    {
        return $this->getVarDumper()->getAssets();
    }
}
