<?php
declare(strict_types=1);

namespace Concrete\Package\MtProfiler\DataCollector;

use DebugBar\DataCollector\AssetProvider;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

/**
 * Class SessionDataCollector
 * @package Concrete\Package\MtProfiler\DataCollector
 */
class SessionDataCollector extends DataCollector implements Renderable, AssetProvider
{
    /**
     * @return array
     */
    function collect(): array
    {
        $data = \Session::all();
        $items = [];

        foreach ($data as $key => $datum) {
            $items[$key] = $this->getVarDumper()->renderVar($datum);
        }

        return $items;
    }

    /**
     * @return string
     */
    function getName(): string
    {
        return 'concrete5session';
    }

    /**
     * @return \string[][]
     */
    function getWidgets(): array
    {
        return [
            'session' => [
                'icon' => 'history',
                'widget' => 'PhpDebugBar.Widgets.HtmlVariableListWidget',
                'map' => 'concrete5session',
                'default' => '{}'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getAssets(): array
    {
        return $this->getVarDumper()->getAssets();
    }

}
