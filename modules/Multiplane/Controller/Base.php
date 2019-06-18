<?php

namespace Multiplane\Controller;

class Base extends \LimeExtra\Controller {

    public function before() {
        
        $this->app->module('multiplane')->getSite();

    }

    public function index($slug = '') {

        $page = $this->module('multiplane')->findOne($slug);
        $posts = null;
        $site = $this->module('multiplane')->site;

        if (!$page) return false;

        $hasSubpageModule = isset($page['subpagemodule']['active'])
                            && $page['subpagemodule']['active'] === true;

        if ($hasSubpageModule) {
            
            $options = $page['subpagemodule'];

            if ($this->module('multiplane')->pageTypeDetection == 'collections') {

                $collection = $page['subpagemodule']['collection'] ?? null;

                $posts = $this->module('multiplane')->getPosts($collection, $this->app->module('multiplane')->currentSlug, $options);
            }

            elseif ($this->module('multiplane')->pageTypeDetection == 'type') {

                $type = $page['subpagemodule']['type'] ?? 'post';

                $posts = $this->module('multiplane')->getPostsByType($type, $this->app->module('multiplane')->currentSlug, $options);
            }

        }

        // custom views
        $view = 'views:index.php';
        if ($this->module('multiplane')->pageTypeDetection == 'collections') {
            if ($path = $this->app->path('views:' . $this->module('multiplane')->collection . '.php')) {
                $view = $path;
            }
        }
        if ($this->module('multiplane')->pageTypeDetection == 'type' && !empty($page['type'])) {
            if ($path = $this->app->path('views:' . $page['type'] . '.php')) {
                $view = $path;
            }
        }

        return $this->render($view, compact('page', 'posts', 'site'));

    }

    public function livePreview($params = []) {

        $page = [];
        $site = [];
        $posts = null;

        // fix language specific paths
        if ($this->app->module('multiplane')->isMultilingual) {
            $this->app->set('base_url', MP_BASE_URL . '/' . $this('i18n')->locale);
        }

        return $this->render('views:live-preview.php', compact('page', 'posts', 'site'));

    }

    public function getPreview($data = []) {

        return $this->app->module('multiplane')->getPreview($data);

    }

    public function getImage($options = []) {

        $src = $this->param('src', null);

        if (!$src) return false;

        // lazy uploads prefix if src is an assets id (has no dot in filename) or is mp asset
        if (strpos($src, '/modules/Monoplane') !== 0 && strpos($src, '.') !== false) {
            $src = '#uploads:'.$src;
        }

        $options = [
            'src' => $src,
            'mode' => $this->escape($this->param('m', 'bestFit')),
            'width' => intval($this->param('w', 800)),
            'height' => intval($this->param('h', null)),
            'quality' => intval($this->param('q', 80)),
        ];

        if ($this->param('blur')) {
            $options['filters']['blur'] = ['type' => 'gaussian', 'passes' => intval($this->param('blur', 5))];
        }

        $thumbpath = $this->module('cockpit')->thumbnail($options);

        $ext = strtolower(pathinfo($thumbpath, PATHINFO_EXTENSION));

        $store = $ext == 'svg' ? 'uploads://' : 'thumbs://';
        $thumbpath = $store . '/' . str_replace($this->app->filestorage->getUrl($store), '', $thumbpath);

        $timestamp = $this->app->filestorage->getTimestamp($thumbpath);
        $gmt_timestamp = gmdate(DATE_RFC1123, $timestamp);

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == strtotime($gmt_timestamp)) {
            header('HTTP/1.1 304 Not Modified');
            $this->app->stop();
        }

        $mime = \Lime\App::$mimeTypes[$ext];

        header("Content-Type: " . $mime);
        header('Content-Length: '.$this->app->filestorage->getSize($thumbpath));
        header('Last-Modified: ' . $gmt_timestamp);
        header('Expires: ' . gmdate(DATE_RFC1123, time() + 31556926));
        header('Cache-Control: max-age=31556926');
        header('Pragma: max-age=31556926');

        echo $this->app->filestorage->read($thumbpath);

        $this->app->stop();

    }

    public function error($status = '') {

        // To do: 401, 500

        switch ($status) {
            case '404':
                return $this->render('views:errors/404.php', ['page' => []]);
                break;
            case '503':
                $this->app->layout = null;
                return $this->render('views:errors/503-maintenance.php', ['site' => cockpit('multiplane')->site]);
                break;
        }

    }

}
