<?php

namespace Multiplane\Controller;

class CockpitLivePreview extends \LimeExtra\Controller {

    public function index() {}

    public function livePreview($params = []) {

        $page = [
            'title' => 'Live preview',
        ];
        $site = $this->app->module('multiplane')->getSite();
        // $posts = null;
        $_posts = [];

        if ($this->app->module('multiplane')->hasBackgroundImage) {
            $this->app->module('multiplane')->addBackgroundImage();
        }

        // fix language specific paths + i18n
        if ($this->app->module('multiplane')->isMultilingual) {

            $lang = $this->app->module('multiplane')->lang;

            // init + load i18n
            if ($translationspath = $this->app->path("mp_config:i18n/{$lang}.php")) {
                $this('i18n')->load($translationspath, $lang);
            }

            $this->app->set('base_url', MP_BASE_URL . '/' . $lang);

        }

        $this->app->viewvars['page']       = $page;
        $this->app->viewvars['site']       = $site;
        $this->app->viewvars['posts']      = $_posts['posts']      ?? [];
        $this->app->viewvars['pagination'] = $_posts['pagination'] ?? [];

        $this->app->viewvars['_meta']['posts_collection'] = $_posts['collection'] ?? [];

        return $this->render('views:layouts/live-preview.php');

    }

    public function getPreview($data = []) {

        $data = \class_exists('\Lime\Request') ? $this->app->request->request : $_REQUEST;

        $event      = $data['event'] ?? false;

        if ($event != 'cockpit:collections.preview') return false;

        $lang       = isset($data['lang']) && $data['lang'] != 'default'
                      ? $data['lang'] : $this->app->module('multiplane')->defaultLang;
        $page       = $data['entry'] ?? false;
        $collection = $data['collection'] ?? false;

        $_posts = [];
        $site  = $this->app->module('multiplane')->site;

        $slugName = $this->app->module('multiplane')->fieldNames['slug'];

        if ($this->app->module('multiplane')->isMultilingual) {
            $this->app->module('multiplane')->initI18n($lang);
        }

        if ($lang != 'default') {

            $page = $this->app->module('collections')->_filterFields($page, $collection, ['lang' => $lang]);

        }

        $this->app->module('multiplane')->_doChecksWithCurrentPage($page);

        if (!empty($this->app->module('multiplane')->preRenderFields) && \is_array($this->app->module('multiplane')->preRenderFields)) {
            $page = $this->app->module('multiplane')->renderFields($page);
        }

        $hasSubpageModule = isset($page['subpagemodule']['active'])
                            && $page['subpagemodule']['active'] === true;

        if ($hasSubpageModule) {

            $subCollection = $page['subpagemodule']['collection'];
            // $route = $page['subpagemodule']['route'] ?? $page[$slugName];
            $_posts = $this->app->module('multiplane')->getPosts($subCollection, $this->app->module('multiplane')->currentSlug);

        }

        $this->app->trigger('multiplane.getpreview.before', [$collection, &$page, &$_posts, &$site]);

        $this->app->viewvars['page']       = $page;
        $this->app->viewvars['site']       = $site;
        $this->app->viewvars['posts']      = $_posts['posts']      ?? [];
        $this->app->viewvars['pagination'] = $_posts['pagination'] ?? [];

        $this->app->viewvars['_meta']['posts_collection'] = $_posts['collection'] ?? [];

        if ($this->app->module('multiplane')->previewMethod == 'json') {
            return compact('page', 'posts', 'site');
        }

        elseif ($this->app->module('multiplane')->previewMethod == 'html') {
            $olayout = $this->app->layout;
            $this->app->layout = false;

            $view = 'views:layouts/default.php';
            if ($path = $this->app->path("views:layouts/collections/{$collection}.php")) {
                $view = $path;
            }

            $content = $this->app->view($view);

            $this->app->layout = $olayout;

            return $content;
        }

        return false;

    }

}
