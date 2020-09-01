<?php
declare(strict_types=1);

namespace Concrete\Package\MtProfiler\Events;

use Concrete\Core\Application\Application;
use Concrete\Package\MtProfiler\Debugbar;

/**
 * Class Listeners
 * @package Concrete\Package\MtProfiler\Events
 */
class Listeners
{
    private const PLACEHOLDER_TEXT = '<!-- debugbar:placeholder -->';

    private Application $app;

    /**
     * Listeners constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function listen(): void
    {
        $dispatcher = $this->app->make('director');

        $dispatcher->addListener('on_before_dispatch', function () {
            $this->app->make(Debugbar::class);
        });

        $dispatcher->addListener('on_before_render', function ($event) {
            $v = $event->getArgument('view');
            $v->addHeaderItem($this->app->make('debugbar/renderer')->renderHead());
            $v->addFooterItem(self::PLACEHOLDER_TEXT);
        });

        $dispatcher->addListener('on_page_output', function ($event) {
            $contents = $event->getArgument('contents');
            $contents = str_replace(self::PLACEHOLDER_TEXT, $this->app->make('debugbar/renderer')->render(), $contents);
            $event->setArgument('contents', $contents);
        });
    }
}
