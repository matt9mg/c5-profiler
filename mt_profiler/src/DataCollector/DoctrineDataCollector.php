<?php
declare(strict_types=1);

namespace Concrete\Package\MtProfiler\DataCollector;

use DebugBar\Bridge\DoctrineCollector;

/**
 * Class DoctrineDataCollector
 * @package Concrete\Package\MtProfiler\DataCollector
 */
class DoctrineDataCollector extends DoctrineCollector
{
    /**
     * @return array
     */
    public function getWidgets(): array
    {
        return array(
            'DB' => array(
                'icon' => 'database',
                'widget' => 'PhpDebugBar.Widgets.SQLQueriesWidget',
                'map' => 'doctrine',
                'default' => '[]'
            ),
            'DB:badge' => array(
                'map' => 'doctrine.nb_statements',
                'default' => 0
            )
        );
    }
}
