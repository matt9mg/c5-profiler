<?php
namespace Concrete\Package\MtProfiler\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

class EnvironmentDataCollector extends DataCollector implements Renderable
{
    public function collect()
    {
        $data['variables'] = $this->getVarDumper()->renderVar(get_defined_vars());
        $data['server']    = $this->getVarDumper()->renderVar($_SERVER);
        $data['classes']   = $this->getVarDumper()->renderVar(get_declared_classes());
        $data['functions'] = $this->getVarDumper()->renderVar(get_defined_functions());
        $data['constants'] = $this->getVarDumper()->renderVar(get_defined_constants());

        return $data;
    }

    public function getName()
    {
        return 'env';
    }

    public function getWidgets()
    {
        return [
            "env" => [
                "icon" => "globe",
                "widget" => "PhpDebugBar.Widgets.HtmlVariableListWidget",
                "map" => "env",
                "default" => "{}",
            ],
        ];
    }
}
