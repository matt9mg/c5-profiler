<?php
declare(strict_types=1);
namespace Concrete\Package\MtProfiler\Renderer;

use Concrete\Core\Http\Request;
use DebugBar\DebugBar;
use DebugBar\JavascriptRenderer as BaseJavascriptRenderer;

/**
 * {@inheritdoc}
 */
class JsRenderer extends BaseJavascriptRenderer
{
    // Use XHR handler by default, instead of jQuery
    protected $ajaxHandlerBindToJquery = false;
    protected $ajaxHandlerBindToXHR = true;

    public function __construct(DebugBar $debugBar, string $baseUrl = null, string $basePath = null)
    {
        parent::__construct($debugBar, $baseUrl, $basePath);

        $this->cssFiles['mt-profiler'] = __DIR__ . '/../Resources/mt-profiler-debugbar.css';
        $this->cssVendors['fontawesome'] = __DIR__ . '/../Resources/vendor/font-awesome/style.css';
        $this->cssFiles['mt-profiler-dark'] = __DIR__ . '/../Resources/mt-profiler-debugbar-dark-mode.css';
    }

    /**
     * @return string
     */
    public function renderHead(): string
    {
        $baseUrl = Request::getInstance()->getBaseUrl();
        $cssRoute = $baseUrl . '/mt_profiler/assets/css?v=' . $this->getModifiedTime('css');

        $jsRoute =  $baseUrl . '/mt_profiler/assets/js?v=' . $this->getModifiedTime('js');

        $cssRoute = preg_replace('/\Ahttps?:/', '', $cssRoute);
        $jsRoute  = preg_replace('/\Ahttps?:/', '', $jsRoute);

        $html  = "<link rel='stylesheet' type='text/css' property='stylesheet' href='{$cssRoute}'>";
        $html .= "<script type='text/javascript' src='{$jsRoute}'></script>";

        if ($this->isJqueryNoConflictEnabled()) {
            $html .= '<script type="text/javascript">jQuery.noConflict(true);</script>' . "\n";
        }

        $html .= $this->getInlineHtml();


        return $html;
    }

    /**
     * @return string
     */
    protected function getInlineHtml(): string
    {
        $html = '';

        foreach (['head', 'css', 'js'] as $asset) {
            foreach ($this->getAssets('inline_' . $asset) as $item) {
                $html .= $item . "\n";
            }
        }

        return $html;
    }
    /**
     * Get the last modified time of any assets.
     *
     * @param string $type 'js' or 'css'
     * @return int
     */
    protected function getModifiedTime(string $type): int
    {
        $files = $this->getAssets($type);

        $latest = 0;
        foreach ($files as $file) {
            $mtime = filemtime($file);
            if ($mtime > $latest) {
                $latest = $mtime;
            }
        }
        return $latest;
    }

    /**
     * Return assets as a string
     *
     * @param string $type 'js' or 'css'
     * @return string
     */
    public function dumpAssetsToString(string $type): string
    {
        $files = $this->getAssets($type);

        $content = '';
        foreach ($files as $file) {
            $content .= file_get_contents($file) . "\n";
        }

        return $content;
    }

    /**
     * Makes a URI relative to another
     *
     * @param string|array $uri
     * @param string $root
     * @return string
     */
    protected function makeUriRelativeTo($uri, $root)
    {
        if (!$root) {
            return $uri;
        }

        if (is_array($uri)) {
            $uris = [];
            foreach ($uri as $u) {
                $uris[] = $this->makeUriRelativeTo($u, $root);
            }
            return $uris;
        }

        if ($uri !== null && (substr($uri, 0, 1) === '/' || preg_match('/^([a-zA-Z]+:\/\/|[a-zA-Z]:\/|[a-zA-Z]:\\\)/', $uri))) {
            return $uri;
        }
        return rtrim($root, '/') . "/$uri";
    }
}
