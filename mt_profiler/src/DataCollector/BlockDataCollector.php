<?php
declare(strict_types=1);

namespace Concrete\Package\MtProfiler\DataCollector;

use Concrete\Core\Block\Events\BlockBeforeRender;
use DebugBar\DataCollector\AssetProvider;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class BlockDataCollector
 * @package Concrete\Package\MtProfiler\DataCollector
 */
class BlockDataCollector extends DataCollector implements Renderable, AssetProvider
{
    private array $data = [];

    /**
     * @param BlockBeforeRender null $event
     * @param string|null $name
     */
    public function onWildcardEvent(BlockBeforeRender $event = null, string $name = null): void
    {
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

    /**
     * @param EventDispatcher $events
     */
    public function subscribe(EventDispatcher $events): void
    {
        $this->events = $events;

        $this->events->addListener('on_block_before_render', [$this, 'onWildcardEvent']);
    }

    /**
     * @return array
     */
    public function collect(): array
    {
        return [
            'records' => $this->data,
            'count' => \count($this->data)
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'blocks';
    }

    /**
     * @return \string[][]
     */
    public function getWidgets(): array
    {
        return [
            'blocks' => [
                'icon' => 'cubes',
                'widget' => 'PhpDebugBar.Widgets.HtmlVariableListWidget',
                'map' => 'blocks.records',
                'default' => '{}'
            ],
            'blocks:badge' => array(
                'map' => 'blocks.count',
                'default' => 'null'
            )
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
