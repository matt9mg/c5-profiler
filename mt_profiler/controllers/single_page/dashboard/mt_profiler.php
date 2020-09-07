<?php
declare(strict_types=1);

namespace Concrete\Package\MtProfiler\Controller\SinglePage\Dashboard;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Routing\RedirectResponse;

/**
 * Class MtProfiler
 * @package Concrete\Package\MtProfiler\Controller\SinglePage\Dashboard
 */
class MtProfiler extends DashboardPageController
{
    public function view()
    {
        /**
         * @var Repository $config
         */
        $config = $this->app->make('config');

        if ($this->request->isPost()) {

            $config->save('mt_profiler.active', (bool)$this->request->request->get('active', false));
            $config->save('mt_profiler.profilers.php_info', (bool)$this->request->request->get('php_info', false));
            $config->save('mt_profiler.profilers.messages', (bool)$this->request->request->get('messages', false));
            $config->save('mt_profiler.profilers.time', (bool)$this->request->request->get('time', false));
            $config->save('mt_profiler.profilers.memory', (bool)$this->request->request->get('memory', false));
            $config->save('mt_profiler.profilers.request', (bool)$this->request->request->get('request', false));
            $config->save('mt_profiler.profilers.session', (bool)$this->request->request->get('session', false));
            $config->save('mt_profiler.profilers.monolog', (bool)$this->request->request->get('monolog', false));
            $config->save('mt_profiler.profilers.db', (bool)$this->request->request->get('db', false));
            $config->save('mt_profiler.profilers.logs', (bool)$this->request->request->get('logs', false));
            $config->save('mt_profiler.profilers.env', (bool)$this->request->request->get('env', false));
            $config->save('mt_profiler.profilers.events', (bool)$this->request->request->get('events', false));
            $config->save('mt_profiler.profilers.config', (bool)$this->request->request->get('config', false));
            $config->save('mt_profiler.profilers.route', (bool)$this->request->request->get('route', false));
            $config->save('mt_profiler.profilers.user', (bool)$this->request->request->get('user', false));
            $config->save('mt_profiler.profilers.blocks', (bool)$this->request->request->get('blocks', false));
            $config->save('mt_profiler.profilers.mail', (bool)$this->request->request->get('mail', false));

            return new RedirectResponse('/dashboard/mt_profiler');
        }

        $this->set('title', t('MT Profiler'));
        $this->set('active', $config->get('mt_profiler.active'));
        $this->set('phpInfo', $config->get('mt_profiler.profilers.php_info'));
        $this->set('messages', $config->get('mt_profiler.profilers.messages'));
        $this->set('time', $config->get('mt_profiler.profilers.time'));
        $this->set('memory', $config->get('mt_profiler.profilers.memory'));
        $this->set('request', $config->get('mt_profiler.profilers.request'));
        $this->set('session', $config->get('mt_profiler.profilers.session'));
        $this->set('monolog', $config->get('mt_profiler.profilers.monolog'));
        $this->set('db', $config->get('mt_profiler.profilers.db'));
        $this->set('logs', $config->get('mt_profiler.profilers.logs'));
        $this->set('env', $config->get('mt_profiler.profilers.env'));
        $this->set('events', $config->get('mt_profiler.profilers.events'));
        $this->set('config', $config->get('mt_profiler.profilers.config'));
        $this->set('route', $config->get('mt_profiler.profilers.route'));
        $this->set('user', $config->get('mt_profiler.profilers.user'));
        $this->set('blocks', $config->get('mt_profiler.profilers.blocks'));
        $this->set('mail', $config->get('mt_profiler.profilers.mail'));
    }
}
