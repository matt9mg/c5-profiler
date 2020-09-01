<?php
declare(strict_types=1);

namespace Concrete\Package\MtProfiler\DataCollector;

use Concrete\Core\Logging\LogList;
use DebugBar\DataCollector\AssetProvider;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

/**
 * Class MailDataCollector
 * @package Concrete\Package\MtProfiler\DataCollector
 */
class MailDataCollector extends DataCollector implements Renderable, AssetProvider
{
    /**
     * @return array
     */
    public function collect(): array
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

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'mail';
    }

    /**
     * @return \string[][]
     */
    public function getWidgets(): array
    {
        return [
            'mail' => [
                'icon' => 'inbox',
                'widget' => 'PhpDebugBar.Widgets.HtmlVariableListWidget',
                'map' => 'mail.records',
                'default' => '{}'
            ],
            'mail:badge' => [
                'map' => 'mail.count',
                'default' => 'null'
            ]
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
