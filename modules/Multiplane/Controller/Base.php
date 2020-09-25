<?php

namespace Multiplane\Controller;

class Base extends \LimeExtra\Controller {

    public function before() {

        // load site data from site singleton
        $this->app->module('multiplane')->getSite();

    }

    public function index($slug = '') {

        $page  = $this->module('multiplane')->getPage($slug);

        if (!$page) return false;

        $posts = null;
        $site  = $this->module('multiplane')->site;

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
        $view = 'views:layouts/default.php';
        if ($this->module('multiplane')->pageTypeDetection == 'collections') {
            $currentCollectionName = $this->module('multiplane')->collection;
            if ($path = $this->app->path("views:layouts/collections/{$currentCollectionName}.php")) {
                $view = $path;
            }
        }
        if ($this->module('multiplane')->pageTypeDetection == 'type' && !empty($page['type'])) {
            if ($path = $this->app->path("views:layouts/types/{$page['type']}.php")) {
                $view = $path;
            }
        }

        // add canonical, if page has a form
        if (isset($page['contactform']['active']) && $page['contactform']['active'] == true) {
            $this->app->on('multiplane.seo', function(&$seo) use($slug) {
                $seo['canonical'] = $this->baseUrl($slug);
            });
        }

        $this->app->trigger('multiplane.page', [&$page, &$posts, &$site]);

        return $this->render($view, compact('page', 'posts', 'site'));

    } // end of index()

    public function livePreview($params = []) {

        $page = [];
        $site = $this->module('multiplane')->site;
        $posts = null;

        if ($this->app->module('multiplane')->hasBackgroundImage) {
            $this->app->module('multiplane')->addBackgroundImage();
        }

        // fix language specific paths + i18n
        if ($this->app->module('multiplane')->isMultilingual) {

            $lang = $this->app->module('multiplane')->lang;

            // init + load i18n
            if ($translationspath = $this->path("mp_config:i18n/{$lang}.php")) {
                $this('i18n')->load($translationspath, $lang);
            }

            $this->app->set('base_url', MP_BASE_URL . '/' . $lang);

        }

        return $this->render('views:layouts/live-preview.php', compact('page', 'posts', 'site'));

    } // end of livePreview()

    public function getPreview($data = []) {

        return $this->app->module('multiplane')->getPreview($data);

    } // end of getPreview()

    public function getImage($options = []) {

        $src = $this->param('src', null);

        if (!$src) return false;

        // remove `/storage/uploads` from image urls
        $uploads = \str_replace(COCKPIT_ENV_ROOT, '', $this->app->path('#uploads:'));
        if (\strpos($src, $uploads) === 0) {
            $src = \substr($src, strlen($uploads));
        }

        // lazy uploads prefix if src is a path instead of an assets id (has no dot in filename) or is mp asset
        if (\strpos($src, '#') !== 0
            && \strpos($src, '/modules/Multiplane') !== 0
            && \strpos($src, '.') !== false) {
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

        // add checks for ddos protection, allow only certain files, or deliver modified media...
        $this->app->trigger('multiplane.getimage.before', [&$options]);

        // optional: redirect to original file
        if (isset($options['output']) && $options['output'] === true) {
            return $this->module('cockpit')->thumbnail($options);
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

        $mime = (\property_exists('\Lime\App', 'mimeTypes'))
                ? \Lime\App::$mimeTypes[$ext]
                : \Lime\Response::$mimeTypes[$ext];

        header("Content-Type: " . $mime);
        header('Content-Length: '.$this->app->filestorage->getSize($thumbpath));
        header('Last-Modified: ' . $gmt_timestamp);
        header('Expires: ' . gmdate(DATE_RFC1123, time() + 31556926));
        header('Cache-Control: max-age=31556926');
        header('Pragma: max-age=31556926');

        echo $this->app->filestorage->read($thumbpath);

        $this->app->stop();

    } // end of getImage()

    public function search($params = null) {

        $site = $this->module('multiplane')->site;

        $page = [
            'title' => $this('i18n')->get('Search'),
            // 'description' => ''
        ];
        $page['seo']['canonical'] = $this->app->baseUrl('/search');

        if ($this->app->module('multiplane')->hasBackgroundImage) {
            $this->app->module('multiplane')->addBackgroundImage();
        }

        $return = $this->app->helper('search')->search($params);

        // make $list, $query, $error, $count available
        extract($return);

        return $this->render('views:layouts/search.php', compact('page', 'site', 'list', 'error', 'count'));

    } // end of search()

    public function sitemap() {

        $xml = new \XMLWriter();
        $xml->openMemory();

        $xml->setIndent(true);

        $xml->startDocument('1.0', 'UTF-8');

        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        if (!$this->app->module('multiplane')->isMultilingual) {
            $xml->writeAttribute('xmlns:xhtml', 'http://www.w3.org/1999/xhtml');
        } else {
            $xml->writeAttribute('xmlns:xhtml', 'http://www.w3.org/TR/xhtml11/xhtml11_schema.html');
        }

        $xml->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $xml->writeAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');

        $this->app->trigger('multiplane.sitemap', [&$xml]);

        $xml->endElement(); // end urlset

        $xml->endDocument();

        $this->app->response->mime = 'xml';

        return $xml->outputMemory();

    } // end of sitemap()

    public function error($status = '') {

        $site = $this->module('multiplane')->site;
        $page = [];

        if ($this->app->module('multiplane')->hasBackgroundImage) {
            $this->app->module('multiplane')->addBackgroundImage();
        }

        // To do: 401, 500

        switch ($status) {
            case '404':
                return $this->render('views:errors/404.php', compact('site', 'page'));
                break;
            case '503':
                $this->app->layout = null;
                return $this->render('views:errors/503-maintenance.php', compact('site'));
                break;
        }

    } // end of error()

}
