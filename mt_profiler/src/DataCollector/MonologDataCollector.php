<?php
declare(strict_types=1);

namespace Concrete\Package\MtProfiler\DataCollector;

use DebugBar\Bridge\MonologCollector;

/**
 * Class MonologDataCollector
 * @package Concrete\Package\MtProfiler\DataCollector
 */
class MonologDataCollector extends MonologCollector
{
    /**
     * @return \string[][]
     */
    public function getWidgets(): array
    {
        $name = $this->getName();
        return [
            $name => [
                'icon' => 'edit',
                'widget' => 'PhpDebugBar.Widgets.MessagesWidget',
                'map' => $name . '.records',
                'default' => '[]'
            ],
            $name . ':badge' => [
                'map' => $name . '.count',
                'default' => 'null'
            ]
        ];
    }
}
