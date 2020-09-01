<?php
namespace MtProfiler\Controller\Api;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Package\MtProfiler\Debugbar;
use DebugBar\OpenHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OpenController implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;


    public function handle()
    {
        $debugbar = $this->app->make(Debugbar::class);

        $openHandler = new OpenHandler($debugbar);
        $data = $openHandler->handle(null, false, false);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}
