<?php
declare(strict_types=1);
namespace Concrete\Package\MtProfiler\DataCollector;

use Concrete\Core\Http\Request;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

/**
 * Class EnvironmentDataCollector
 * @package Concrete\Package\MtProfiler\DataCollector
 */
class EnvironmentDataCollector extends DataCollector implements Renderable
{
    /**
     * @return array
     */
    public function collect(): array
    {
        $data['variables'] = $this->getVarDumper()->renderVar(get_defined_vars());
        $data['server']    = $this->getVarDumper()->renderVar(Request::getInstance()->server->all());
        $data['classes']   = $this->getVarDumper()->renderVar(get_declared_classes());
        $data['functions'] = $this->getVarDumper()->renderVar(get_defined_functions());
        $data['constants'] = $this->getVarDumper()->renderVar(get_defined_constants());

        return $data;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'env';
    }

    /**
     * @return \string[][]
     */
    public function getWidgets(): array
    {
        return [
            'env' => [
                'icon' => 'globe',
                'widget' => 'PhpDebugBar.Widgets.HtmlVariableListWidget',
                'map' => 'env',
                'default' => '{}',
            ],
        ];
    }
}
