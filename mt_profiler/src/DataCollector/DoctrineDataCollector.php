<?php
/*
 * This file is part of the DebugBar package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Concrete\Package\MtProfiler\DataCollector;

use DebugBar\Bridge\DoctrineCollector;

/**
 * Collects Doctrine queries
 *
 * http://doctrine-project.org
 *
 * Uses the DebugStack logger to collects data about queries
 *
 * <code>
 * $debugStack = new Doctrine\DBAL\Logging\DebugStack();
 * $entityManager->getConnection()->getConfiguration()->setSQLLogger($debugStack);
 * $debugbar->addCollector(new DoctrineCollector($debugStack));
 * </code>
 */
class DoctrineDataCollector extends DoctrineCollector
{
    /**
     * @return array
     */
    public function getWidgets()
    {
        return array(
            "DB" => array(
                "icon" => "database",
                "widget" => "PhpDebugBar.Widgets.SQLQueriesWidget",
                "map" => "doctrine",
                "default" => "[]"
            ),
            "DB:badge" => array(
                "map" => "doctrine.nb_statements",
                "default" => 0
            )
        );
    }
}
