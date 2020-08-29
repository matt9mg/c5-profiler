<?php


namespace Concrete\Package\MtProfiler\DataCollector;

use Concrete\Core\Block\Block;
use DebugBar\DataCollector\AssetProvider;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Symfony\Component\EventDispatcher\EventDispatcher;

class BlockDataCollector extends DataCollector implements Renderable, AssetProvider
{
    private $data = [];

    public function onWildcardEvent($event = null, $name = null)
    {
        /**
         * @var Block $block
         */
        $block = $event->getSubject();

        $block->getBlockAreaObject()->getAreaDisplayName();

        $areaType = $block->getBlockAreaObject()->isGlobalArea() ? 'Global Area' : 'Area';

        $this->data[strtoupper($areaType) . ': ' . $block->getBlockAreaObject()->getAreaDisplayName() . ' | bID ' . $block->getBlockID()] = [
            $this->getVarDumper()->renderVar([
            'Block Name' => $block->getController()->getBlockTypeName(),
            'Block Id' => $block->getBlockID(),
            'Template' => $block->getBlockFilename() ?: 'view.php',
            'Area Type' => $areaType,
            'Dump' => $event->getSubject()
        ])];
    }

    public function subscribe(EventDispatcher $events)
    {
        $this->events = $events;

        $this->events->addListener('on_block_before_render', [$this, 'onWildcardEvent']);
    }

    public function collect()
    {
        return [
            'records' => $this->data,
            'count' => \count($this->data)
        ];
    }

    public function getName()
    {
        return 'blocks';
    }

    public function getWidgets()
    {
        return [
            "blocks" => [
                "icon" => "cubes",
                "widget" => "PhpDebugBar.Widgets.HtmlVariableListWidget",
                "map" => "blocks.records",
                "default" => "{}"
            ],
            "blocks:badge" => array(
                "map" => "blocks.count",
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
