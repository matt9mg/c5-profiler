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

            return new RedirectResponse('/dashboard/mt_profiler');
        }


        $this->set('title', t('MT Profiler'));
        $this->set('active', $config->get('mt_profiler.active'));
        $this->set('phpInfo', $config->get('mt_profiler.profilers.php_info'));
        $this->set('messages', $config->get('mt_profiler.profilers.messages'));
        $this->set('time', $config->get('mt_profiler.profilers.time'));
        $this->set('memory', $config->get('mt_profiler.profilers.memory'));
    }
}
