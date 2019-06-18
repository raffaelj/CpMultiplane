<?php

// adjust some auto-detected directory routes to current dir, otherwise inbuilt
// functions from Lime\App, like pathToUrl() would return wrong paths
$this->set('docs_root', MP_DOCS_ROOT);
$this->set('base_url', MP_BASE_URL);
$this->set('base_route', MP_BASE_URL); // for reroute()
$this->set('site_url', $this->getSiteUrl(true)); // for pathToUrl(), which is used in thumbnail function

// rewrite filestorage paths to get correct image urls
$this->on('cockpit.filestorages.init', function(&$storages) {
    $storages['uploads']['url'] = $this->pathToUrl('#uploads:', true);
    $storages['thumbs']['url'] = $this->pathToUrl('#thumbs:', true);
});

// set paths
$this->path('views', __DIR__.'/themes/rljbase');
$this->path('mp_config', MP_DOCS_ROOT . '/config');

// register autoload classes in namespace Multiplane\Controller from
// `MP_DOCS_ROOT/Controller`, e. g.: `/Controller/Products.php`
spl_autoload_register(function($class){
    $class_path = MP_DOCS_ROOT.'/Controller'.str_replace(['Multiplane\Controller', '\\'], ['', '/'], $class).'.php';
    if(file_exists($class_path)) include_once($class_path);
});

// add helpers
$this->helpers['fields'] = 'Multiplane\\Helper\\Fields';


$this->module('multiplane')->extend([

    // base config
    'isMultilingual'        => false,
    'useDefaultRoutes'      => true,
    'outputMethod'          => 'dynamic',                   // to do: static
    'pageTypeDetection'     => 'collections',
    'slugName'              => '_id',

    'isInMaintenanceMode'   => false,
    'allowedIpsInMaintenanceMode' => null,
    'clientIpIsAllowed'   => false,

    // use Fields render helper and optional field templates
    'preRenderFields'       => [],

    // changes dynamically
    'defaultLang'           => $this->retrieve('i18n', 'en'),
    'breadcrumbs'           => ['/'],
    'isStartpage'           => false,

    'site'                  => [],

    'pages'                 => 'pages',
    'pagesPattern'          => '{title}',   // to do...

    'collection'            => 'pages',

    'posts'                 => 'posts',
    'postsPattern'          => '{collection}/{title}', // to do...
    // 'postsPattern'         => '{YYYY}/{MM}/{DD}/{title}', // to do...

    // content preview
    'isPreviewEnabled'      => true,
    'previewMethod'         => 'html',
    'livePreviewToken'      => 'a5aaa86fb37592f02fb14229b706de',
    'previewDelay'          => 0,

    'hasParentPage'         => false,
    'displayPostsLimit'     => 5,       // number of posts to display in subpagemodule
    'paginationDropdownLimit' => 5,     // number of pages, when the pagination turns to dropdown menu

    'styles'                => [],      // access via cockpit('multiplane')->userStyles();
    'scripts'               => [],

    'hasBackgroundImage'    => false,

    'set' => function($key, $value) {

        $this->$key = $value;

    },

    'add' => function($key, $value) {

        if (is_array($this->$key)) {
            $this->$key = array_merge_recursive($this->$key, $value);
        }

        elseif (is_string($this->$key) && is_string($value)) {
            $this->$key .= $value;
        }

        else {
            // do nothing
        }

    },

    'getSite' => function() {

        $site = $this->app->module('singletons')->getData('site');

        if ($site) $this->site = $site;

        return $site;

    },

    'findOne' => function($slug = '') {

        $slug = $this->resolveSlug($slug);

        // startpage
        if (empty($slug)) {

            $this->isStartpage = true;

            $filter = [
                'published' => true,
                'startpage' => true,
            ];

        }
        // filter by slug
        else {
            $filter = [
                'published' => true,
            ];
        
            if (!$this->isMultilingual) {
                $filter[$this->slugName] = $slug;
            }
            else {
                // filter by localized slug
                $lang = $this('i18n')->locale;

                $isLocalized = $this->app->retrieve('unique_slugs/localize/'.$this->collection, false);

                if ($this->slugName != '_id' && $isLocalized && $lang != $this->defaultLang) {
                    $filter[$this->slugName.'_'.$lang] = $slug;
                } else {
                    $filter[$this->slugName] = $slug;
                }
            }
        }

        $page = $this->app->module('collections')->findOne($this->collection, $filter, null, false, ['lang' => $this('i18n')->locale]);

        if (!$page) return false;
        
        if (!empty($this->preRenderFields) && is_array($this->preRenderFields)) {
            $page = $this->renderFields($page);
        }

        if ($this->hasBackgroundImage) {
            $this->addBackgroundImage($page);
        }

        return $page;

    },

    'userStyles' => function() {

        if (empty($this->styles)) return;

        echo "\r\n<style>\r\n";

        foreach ($this->styles as $selector => $style) {
            if (is_numeric($selector) && is_string($style)) {
                echo $style . "\r\n";
                continue;
            }
            elseif (is_string($style)) {
                echo "$selector $style" . "\r\n";
            }
        }

        echo "</style>\r\n";

    },

    'userScripts' => function() {

        if (empty($this->scripts)) return;

        echo "\r\n<script>\r\n";

        foreach ($this->scripts as $script) {
            echo $script . "\r\n";
        }

        echo "</script>\r\n";

    },

    'addBackgroundImage' => function($page = []) {

        // to do: custom media queries

        $background = $page['background_image']['_id']
                   ?? $this->site['background_image']['_id']
                   ?? null;

        if ($background) {

            $css = [];
            $css[] = '@media (min-width: 400px) and (max-width: 1000px) {html {background-image: url("'.MP_BASE_URL.'/getImage?src='.$background.'&w=1000&m=bestFit&q=70");}}';
            $css[] = '@media (min-width: 1000px) and (max-width: 1200px) {html {background-image: url("'.MP_BASE_URL.'/getImage?src='.$background.'&w=1200&m=bestFit&q=70");}}';
            $css[] = '@media (min-width: 1200px) {html {background-image: url("'.MP_BASE_URL.'/getImage?src='.$background.'&w=1920&m=bestFit&q=70");}}';

            $this->add('styles', $css);

        }

    },

    'getPreview' => function() {

        $data = $_REQUEST;

        $event      = $data['event'] ?? false;
        $lang       = $data['lang'] ?? $this->defaultLang;
        $page       = $data['entry'] ?? false;
        $collection = $data['collection'] ?? false;

        $posts = null;
        $site = $this->site;

        if ($event != 'cockpit:collections.preview') return false;

        if ($this->isMultilingual) {
            $this('i18n')->locale = ($lang == 'default') ? $this->defaultLang : $lang;
            $this->app->set('base_url', MP_BASE_URL . '/' . $this('i18n')->locale);
        }

        if ($lang != 'default') {

            $page = $this->app->module('collections')->_filterFields($page, $collection, ['lang' => $lang]);

        }

        if (!empty($this->preRenderFields) && is_array($this->preRenderFields)) {
            $page = $this->renderFields($page);
        }

        // if ($this->hasBackgroundImage) {
            // $this->addBackgroundImage($page);
        // }

        $hasSubpageModule = isset($page['subpagemodule']['active']) && $page['subpagemodule']['active'] === true;

        if ($hasSubpageModule) {
            $collection = $page['subpagemodule']['collection'];
            $route = $page['subpagemodule']['route'];

            $posts = $this->getPosts($collection, $this->currentSlug);

        }

        if ($this->previewMethod == 'json') {
            return compact('page', 'posts', 'site');
        }

        elseif ($this->previewMethod == 'html') {
            $olayout = $this->app->layout;
            $this->app->layout = false;

            $view = 'views:index.php';
            if ($path = $this->app->path('views:' . $collection . '.php')) {
                $view = $path;
            }

            $content = $this->app->view($view, compact('page', 'posts', 'site'));

            $this->app->layout = $olayout;

            return $content;
        }

        return false;

    },

    'getNav' => function($collection = null, $type = '') {

        // to do: nested sub navigations

        if (!$collection) $collection = $this->pages;

        $options = [
            'filter' => [
                'published' => true,
            ],
            'fields' => [
                $this->slugName => true,
                'title' => true,
                'nav' => true,
            ],
        ];

        if (!empty($type)) {
            $options['filter']['nav'] = ['$has' => $type];
        }
        
        if ($this->isMultilingual) {

            $lang = $this('i18n')->locale;

            $options['lang'] = $lang;

            if ($lang != $this->defaultLang) {
                $options['fields']['title_'.$lang] = true;
                if ($this->slugName != '_id') {
                    $options['fields'][$this->slugName.'_'.$lang] = true;
                }
            }

        }

        $nav = $this->app->module('collections')->find($collection, $options);

        return $nav;

    },

    'getLanguageSwitch' => function($_id) {

        $languages   = [];

        foreach($this->app['languages'] as $languageCode => $name) {

            $slug = '';

            $lang = ($languageCode == 'default') ? $this->defaultLang : $languageCode;

            $active = $this('i18n')->locale == $lang;

            if (!$this->isStartpage) {

                $filter = [
                    'published' => true,
                    '_id' => $_id ?? '',
                ];

                $projection = [
                    $this->slugName => true,
                    $this->slugName . '_' . $lang => true
                ];

                $fieldsFilter = [
                    'lang' => $lang
                ];

                $entry = $this->app->module('collections')->findOne($this->collection, $filter, $projection, false, $fieldsFilter);
                if (isset($entry[$this->slugName])) $slug = $entry[$this->slugName];

            }
            

            $subpage = null;

            if ($this->hasParentPage == true) { // was set in resolveSlug()
                $route = $lang == $this->defaultLang ? 'route' : 'route_'.$lang;
                $subpage = $this->parentPage['subpagemodule'][$route] ?? null;
            }

            $languages[] = [
                'code' => $lang,
                'name' => $name,
                'active' => $active,
                'url' => MP_BASE_URL . '/' . $lang . '/' . ($subpage ? $subpage . '/' : '') . $slug,
            ];

        }

        return $languages;

    },

    'getPosts' => function($collection = null, $slug = '', $opts = []) {

        if ($this->pageTypeDetection == 'tpye') {
            return $this->getPostsByType($collection, $slug, $opts);
        }

        if (!$collection) $collection = $this->posts;

        $_collection = $this->app->module('collections')->collection($collection);

        if (!$_collection) return false;

        $lang = $this('i18n')->locale;
        $page = $this->app->param('page', 1);
        $limit = (isset($opts['limit']) && (int)$opts['limit'] ? $opts['limit'] : null) ?? $this->displayPostsLimit ?? 5;
        $skip = ($page - 1) * $limit;
        
        $hidePagination = 

        $filter = [
            'published' => true,
        ];

        $options = [
            'filter' => $filter,
            'lang'  => $lang,
            'limit' => $limit,
            'skip'  => $skip,
            'sort' => [
                '_created' => isset($opts['sort']) && $opts['sort'] ? 1 : -1,
            ],
        ];

        $posts = $this->app->module('collections')->find($collection, $options);

        if (!$posts) {
            // send 404 if no posts found (page too high), may braek the page sometimes
            // $this->app->response->status = 404;
            return;
        }

        if (!empty($this->preRenderFields) && is_array($this->preRenderFields)) {
            foreach($posts as &$post) {
                $post = $this->renderFields($post);
            }
        }

        // subpage module is on startpage without slug
        if (empty($slug) && $this->pageTypeDetection == 'collections' && $this->isStartpage) {

            $parentPage = $this->resolveParentPage();

            $route = 'route' . ($lang == $this->defaultLang ? '' : '_'.$lang);

            $slug = $parentPage['subpagemodule'][$route] ?? '';

        }

        $count = $this->app->module('collections')->count($collection, $filter);

        $pagination =  [
            'count' => $count,
            'page'  => $page,
            'limit' => $limit,
            'pages' => ceil($count / $limit),
            'slug'  => $slug,
            'dropdownLimit' => $opts['dropdownLimit'] ?? $this->paginationDropdownLimit ?? 5,
            'hide' => (!isset($opts['pagination']) || $opts['pagination'] !== true),
        ];

        return compact('posts', 'pagination');

    },
    
    'getPostsByType' => function($type = null, $slug = '', $opts = []) {
        
        if (!$type) $type = 'post';

        $lang = $this('i18n')->locale;
        $page = $this->app->param('page', 1);
        $limit = (isset($opts['limit']) && (int)$opts['limit'] ? $opts['limit'] : null) ?? $this->displayPostsLimit ?? 5;
        $skip = ($page - 1) * $limit;

        $filter = [
            'published' => true,
            'type' => $type,
        ];

        $options = [
            'filter' => $filter,
            'lang'  => $lang,
            'limit' => $limit,
            'skip'  => $skip,
        ];

        $posts = $this->app->module('collections')->find($this->pages, $options);

        if (!$posts) {
            // send 404 if no posts found (paginagion too high), may break the page sometimes
            // $this->app->response->status = 404;
            return;
        }

        if (!empty($this->preRenderFields) && is_array($this->preRenderFields)) {
            foreach($posts as &$post) {
                $post = $this->renderFields($post);
            }
        }

        $count = $this->app->module('collections')->count($this->pages, $filter);

        $pagination =  [
            'count' => $count,
            'page'  => $page,
            'limit' => $limit,
            'pages' => ceil($count / $limit),
            // 'slug'  => $slug,
            'slug'  => '',
            'dropdownLimit' => $opts['dropdownLimit'] ?? $this->paginationDropdownLimit ?? 5,
            'hide' => (!isset($opts['pagination']) || $opts['pagination'] !== true),
        ];

        return compact('posts', 'pagination');

    },

    'resolveSlug' => function($slug = '') {

        // check, if slug type is _id or slug
        // check, if sub page
        // to do...

        if ($slug == '') return $slug;

        // fix routes with ending slash
        $slug = rtrim($slug, '/');

        if (strpos($slug, '/')) {

            $parts = explode('/', $slug);

            $count = count($parts);

            // possible options - to do...:
            // * /page-title
            // * /category/page-title
            // * /collection/page-title
            // * /blog/post-title
            // * /blog/page/2
            // * /blog/2019/06/06/post-title
            // * ...

            if ($this->pageTypeDetection == 'collections') {

                if ($collection = $this->resolveCurrentCollection($parts[0])) {

                    $breadcrumbs = $this->breadcrumbs;
                    foreach($parts as $part) {
                        $breadcrumbs[] = $part;
                    }
                    $this->breadcrumbs = $breadcrumbs;

                    if ($parts[1] == 'page' && $count > 2 && (int)$parts[2]) {
                        // pagination for blog module
                        $slug = $parts[0];
                        $_REQUEST['page'] = $parts[2];
                    }

                    else {
                        $this->hasParentPage = true;
                        $this->collection = $collection;
                        $slug = $parts[1];
                    }

                }

            }

            elseif($this->pageTypeDetection == 'type') {

                if ($parts[0] == 'page' && (int)$parts[1]) {
                    // pagination for blog module
                    $slug = ''; 
                    $_REQUEST['page'] = $parts[1];
                }

            }

        }
        else {
            $breadcrumbs = $this->breadcrumbs;
            $breadcrumbs[] = $slug;
            $this->breadcrumbs = $breadcrumbs;
        }

        $this->currentSlug = $slug;

        return $slug;

    },

    'resolveCurrentCollection' => function($route = '') {

        $parentPage = $this->resolveParentPage($route);

        $this->parentPage = $parentPage;

        $collection = $parentPage['subpagemodule']['collection'] ?? false;

        if (is_string($collection) && $this->app->module('collections')->exists($collection)) {
            return $collection;
        }

        return false;

    },

    'resolveParentPage' => function($route = '') {

        $lang = $this('i18n')->locale;

        $slugName = $this->slugName . ($lang == $this->defaultLang ? '' : '_'.$lang);

        $filter = [
            'published' => true,
            $slugName => $route,
            'subpagemodule.active' => true,
        ];

        $projection = [
            $this->slugName => true,
            $this->slugName . '_' . $lang => true,
            '_id' => false,
            'subpagemodule' => true,
        ];

        if ($this->isStartpage) {

            $filter = [
                'published' => true,
                'startpage' => true,
                'subpagemodule.active' => true,
            ];

        }

        $fieldsFilter = [
            'lang' => $lang
        ];

        $parentPage = $this->app->module('collections')->findOne($this->pages, $filter, $projection, false, $fieldsFilter);

        return $parentPage;

    },

    'renderFields' => function($page) {

        $collection = $this->app->module('collections')->collection($this->collection);

        if (!isset($collection['fields'])) return $page;

        $fields = $collection['fields'];

        foreach ($fields as $field) {

            if (!isset($page[$field['name']])) continue;

            if (!in_array($field['name'], $this->preRenderFields)) continue;

            $cmd = $field['type'] ?? 'text';
            $opts = $field['options'] ?? [];
            $page[$field['name']] = $this('fields')->$cmd($page[$field['name']], $opts);
 
        }

        return $page;

    },

    'accessAllowed' => function() {

        // maintenance mode
        if (!$this->isInMaintenanceMode) return true;

        if (!$ips = $this->allowedIpsInMaintenanceMode) {
            return false;
        }

        else {
            // allow array input or string with white space delimiter
            $ips = is_array($ips) ? $ips : explode(' ', trim($ips));

            if (in_array($this->app->getClientIp(), $ips)) {
                $this->clientIpIsAllowed = true;
                return true;
            }
        }

        return false;

    },

    'getRouteToPrivacyPage' => function() {

        $filter = [
            'published' => true,
            'privacypage' => true
        ];

        $lang = $this('i18n')->locale;

        $projection = [
            $this->slugName => true,
            '_id' => false,
        ];
        if ($this->isMultilingual && $lang != $this->defaultLang) {
            $projection[$this->slugName.'_'.$lang] = true;
        }

        $page = $this->app->module('collections')->findOne($this->pages, $filter, $projection, null, false, ['lang' => $lang]);

        $route = $page[$this->slugName.'_'.$lang] ?? $page[$this->slugName] ?? '';

        return '/'.$route;

    },

]);


// module parts
include_once(__DIR__ . '/module/forms.php');


$this->on('multiplane.init', function() {

    // overwrite default config
    $config = $this->retrieve('multiplane', []);

    foreach($config as $key => $val) {
        $this->module('multiplane')->$key = $val;
    }

    // skip binding routes if in maintenance mode
    if (!$this->module('multiplane')->accessAllowed()) {
        return;
    }

    // dont't bind any routes, if user wants to use only their own routes
    if (!$this->module('multiplane')->useDefaultRoutes) {
        return;
    }

    // bind routes
    $this->bind('/login', function() {
        $this->reroute(MP_ADMINFOLDER);
    });

    $this->bind('/getImage', function() {
        return $this->invoke('Multiplane\\Controller\\Base', 'getImage');
    });

    // routes for live preview
    $this->bind('/getPreview', function($params) {
        return $this->invoke('Multiplane\\Controller\\Base', 'getPreview', ['params' => $params]);
    }, $this->module('multiplane')->isPreviewEnabled && $this->req_is('ajax'));

    $this->bind('/livePreview', function($params) {

        if ($this->param('token') != $this->module('multiplane')->livePreviewToken) {
            return false;
        }

        return $this->invoke('Multiplane\\Controller\\Base', 'livePreview', ['params' => $params]);

    }, $this->module('multiplane')->isPreviewEnabled);

    // bind wildcard routes
    $isMultilingual = $this->module('multiplane')->isMultilingual && ($languages = $this->retrieve('languages', false));

    if (!$isMultilingual) {

        // routes for forms
        $this->bind('/form/*', function($params) {
            return $this->invoke('Multiplane\\Controller\\Forms', 'index', ['params' => $params]);
        });

        $this->bind('/*', function($params) {
            return $this->invoke('Multiplane\\Controller\\Base', 'index', ['slug' => $params[':splat'][0]]);
        });
    }
    else {

        $defaultLang = $this->retrieve('monoplane/i18n') ?? $this->retrieve('i18n', 'en');

        foreach($languages as $languageCode => $name) {

            if ($languageCode == 'default') $lang = $defaultLang;
            else $lang = $languageCode;

            // routes for forms
            $this->bind('/'.$lang.'/form/*', function($params) {
                return $this->invoke('Multiplane\\Controller\\Forms', 'index', ['params' => $params]);
            });

            $this->bind('/'.$lang.'/*', function($params) use($lang) {

                $this('i18n')->locale = $lang;
                $this->set('base_url', MP_BASE_URL . '/' . $lang);

                // init + load i18n
                if ($translationspath = $this->path("mp_config:i18n/{$lang}.php")) {
                    $this('i18n')->load($translationspath, $lang);
                }

                return $this->invoke('Multiplane\\Controller\\Base', 'index', ['slug' => ($params[':splat'][0] ?? '')]);

            });

        }

        // redirect "/" to "/en"
        $this->bind('/*', function($params) use($languages, $defaultLang) {

            $lang = $this->getClientLang($defaultLang);

            if (!array_key_exists($lang, $languages)) {
                $lang = $defaultLang;
            }
            $this->reroute('/' . $lang . '/' . ($params[':splat'][0] ?? ''));

        });

    }

});

// error handling
$this->on('after', function() {

    // force 404 if body is empty
    if (!$this->response->body || $this->response->body === 404) {
        $this->response->status = 404;
    }
 
    if ($this->module('multiplane')->isInMaintenanceMode) {

        if (!$this->module('multiplane')->clientIpIsAllowed) {
            $this->response->status = 503;
        }

    }

    switch($this->response->status){
        case '404':
            $this->response->body = $this->invoke('Multiplane\\Controller\\Base', 'error', ['status' => $this->response->status]);
            break;
        case '503':
            $this->response->headers[] = 'Retry-After: 3600';
            $this->response->body = $this->invoke('Multiplane\\Controller\\Base', 'error', ['status' => $this->response->status]);
            break;
    }

});


// CLI
if (COCKPIT_CLI) {
    $this->path('#cli', __DIR__.'/cli');
}

// load theme bootstrap file
if ($themeBootstrapPath = $this->path('views:bootstrap.php')) {
    include_once($themeBootstrapPath);
}

// load custom bootstrap file
if (file_exists(MP_CONFIG_DIR.'/bootstrap.php')) {
    include_once(MP_CONFIG_DIR.'/bootstrap.php');
}
