<?php

define('MULTIPLANE_VERSION', '0.3.5');

if ($this['debug']) \error_reporting(E_ALL);

// shorthand module call
if (!function_exists('mp')) {
    function mp() {return cockpit('multiplane');}
}

// define some constants to avoid throwing errors if Multiplane is inside
// `addons` dir of cockpit instead of inside `modules` dir of CpMultiplane
if (!defined('MP_ADMINFOLDER'))     define('MP_ADMINFOLDER',  '');
if (!defined('MP_ENV_ROOT'))        define('MP_ENV_ROOT',     COCKPIT_ENV_ROOT);
if (!defined('MP_CONFIG_DIR'))      define('MP_CONFIG_DIR',   MP_ENV_ROOT.'/config');
if (!defined('MP_BASE_URL'))        define('MP_BASE_URL',     COCKPIT_BASE_URL);
if (!defined('MP_ENV_URL'))         define('MP_ENV_URL',      MP_BASE_URL);

// set config path
$this->path('mp_config', MP_CONFIG_DIR);

// set themes paths
if ($shippedThemesPath = $this->path(__DIR__.'/themes')) {
    $this->path('#themes', $shippedThemesPath); // deprecated
}
if ((MP_ENV_ROOT != __DIR__) && $customThemesPath = $this->path(MP_ENV_ROOT.'/themes')) {
    $this->path('#themes', $customThemesPath); // deprecated
}
$this->path('#themes', COCKPIT_PUBLIC_STORAGE_FOLDER . '/themes');

\spl_autoload_register(function($class){

    // register autoload classes in namespace Multiplane\Controller from
    // `MP_DIR/Controller`, e. g.: `/Controller/Products.php`
    $class_path = MP_ENV_ROOT.'/Controller'.\str_replace(['Multiplane\Controller', '\\'], ['', '/'], $class).'.php';
    if (\file_exists($class_path)) include_once($class_path);

    // autoload from /modules/Multiplane/lib
    $class_path = __DIR__.'/lib/'.$class.'.php';
    if (\file_exists($class_path)) include_once($class_path);

});

// add helpers
$this->helpers['fields']  = 'Multiplane\\Helper\\Fields';
$this->helpers['search']  = 'Multiplane\\Helper\\Search';
$this->helpers['mputils'] = 'Multiplane\\Helper\\Utils';


$this->module('multiplane')->extend([

    'version' => $this['debug'] ? \time() : MULTIPLANE_VERSION,

    // base config
    'theme'                 => 'rljbase',
    'faviconPath'           => '/favicon.png',
    'parentTheme'           => null,
    'parentThemeBootstrap'  => true,

    'isMultilingual'        => false,
    'usePermalinks'         => false,             // use permalinks (experimental)
    'disableDefaultRoutes'  => false,             // don't use any default routes
    'outputMethod'          => 'dynamic',         // to do: static or pseudo static/cached
    'pageTypeDetection'     => 'collections',     // 'collections' or 'type'
    'nav'                   => null,              // hard coded navigation

    'use' => [
        'collections' => [],                      // list of collection names
        'singletons'  => [],                      // list of singleton names
        'forms'       => [],                      // list of form names
    ],

    'structure'             => [],

    'slugName'              => '_id',             // deprecated, field name for url slug
    'navName'               => 'nav',             // deprecated, field name for navigation

    'fieldNames' => [                             // field mappings to default field names
        'slug'              => '_id',
        'nav'               => 'nav',
        'permalink'         => 'permalink',
        'published'         => 'published',
        'startpage'         => 'startpage',
        'title'             => 'title',
        'content'           => 'content',
        'description'       => 'description',
        'excerpt'           => 'excerpt',
        'type'              => 'type',            // only if pageTypeDetection == 'type'
        'subpagemodule'     => 'subpagemodule',
        'privacypage'       => 'privacypage',
        'seo'               => 'seo',
        'featured_image'    => 'featured_image',
        'background_image'  => 'background_image',
        'logo'              => 'logo',            // only in site
        'tags'              => 'tags',
        'category'          => 'category',        // not used for now, will be like tags
        'contactform'       => 'contactform',
    ],

    // maintenance mode
    'isInMaintenanceMode'   => false,             // display under construction page with 503 status
    'allowedIpsInMaintenanceMode' => null,        // separate multiple ip addresses with whitespaces

    'styles'                => [],                // access via mp()->userStyles();
    'scripts'               => [],                // access via mp()->userScripts();

    // use Fields render helper and optional field templates
    'preRenderFields'       => [],

    'site'                  => [],                // default site config
    'siteSingleton'         => '',                // singleton name for default config

    'pages'                 => '',                // collection name for pages
    // 'pagesPattern'          => '{title}',         // to do...

    'posts'                 => '',                // collection name for posts
    // 'postsPattern'          => '{collection}/{title}',        // to do...
    // 'postsPattern'         => '{YYYY}/{MM}/{DD}/{title}',  // to do...

    // content preview
    'isPreviewEnabled'      => false,
    'previewMethod'         => 'html',            // the inbuilt live preview renders the main part as html
    'livePreviewToken'      => md5(__DIR__),
    'previewDelay'          => 0,
    'previewScripts'        => false,           // restart MP init scripts

    // pagination
    'paginationUriDelimiter' => 'page',
    'displayPostsLimit'     => 5,               // number of posts to display in subpagemodule
    'paginationDropdownLimit' => 5,             // number of pages, when the pagination turns to dropdown menu

    'lexy'                  => [],              // extend Lexy parser for image url templates

    // breadcrumbs
    'displayBreadcrumbs'    => false,

    // experimental full text search
    'search' => [
        'enabled'     => false,
        'minLength'   => 3,                     // minimum character length for search
        'collections' => [],                    // full list of collections to search in,
                                                // defaults to "multiplane/use/collections"
    ],

    'sitemap'               => null,            // array of collections

    'hasBackgroundImage'    => false,           // enable background image
    'backgroundBreakpoints' => [
        'mini' => [
            'points' => [
                'max-width' => 500,
            ],
            'size' => 700,
        ],
        'small' => [
            'points' => [
                'min-width' => 500,
                'max-width' => 1000,
            ],
            'size' => 1200,
        ],
        'normal' => [
            'points' => [
                'min-width' => 1000,
                'max-width' => 1200,
            ],
            'size' => 1400,
        ],
        'large' => [
            'points' => [
                'min-width' => 1200,
            ],
            'size' => 1920,
        ],
    ],

    'disableTrailingSlashRedirect' => false,
    'statusCodeForTrailingSlashRoutes' => 301, // (int) 404 Not Found | 301 Moved Permanently |302 Found

    // changes dynamically
    'defaultLang'           => $this->retrieve('multiplane/i18n', $this->retrieve('i18n', 'en')),
    'lang'                  => $this->helper('i18n')->locale,
    'breadcrumbs'           => [['title' => $this->helper('i18n')->get('Home'), 'slug' => '/']],
    'isStartpage'           => false,
    'collection'            => null,            // current collection
    'clientIpIsAllowed'     => false,           // if maintenance and ip is allowed
    'hasParentPage'         => false,           // for sub pages and pagination
    'parentPage'            => null,            // contains info about parent page
    'themePath'             => null,
    'parentThemePath'       => null,

    'set' => function($key, $value) {

        if (\is_array($this->$key) && \is_array($value)) {
            $this->$key = \array_replace_recursive($this->$key, $value);
        } else {
            $this->$key = $value;
        }

    }, // end of set()

    'add' => function($key, $value, $recursive = false) {

        if (\is_array($this->$key) && \is_array($value)) {
            if ($recursive) $this->$key = \array_merge_recursive($this->$key, $value);
            else            $this->$key = \array_merge($this->$key, $value);
        }

        elseif (\is_string($this->$key) && \is_string($value)) {
            $this->$key .= $value;
        }

        else {
            // do nothing
        }

    }, // end of add()

    // modified version of Lime\fetch_from_array()
    'get' => function($index, $default = null) {

        if (\is_null($index)) {

            return null;

        } elseif (isset($this->$index)) {

            return $this->$index;

        } elseif (\strpos($index, '/')) {

            $keys = \explode('/', $index);

            switch (\count($keys)){

                case 1:
                    if (isset($this->{$keys[0]})){
                        return $this->{$keys[0]};
                    }
                    break;

                case 2:
                    if (isset($this->{$keys[0]}[$keys[1]])){
                        return $this->{$keys[0]}[$keys[1]];
                    }
                    break;

                case 3:
                    if (isset($this->{$keys[0]}[$keys[1]][$keys[2]])){
                        return $this->{$keys[0]}[$keys[1]][$keys[2]];
                    }
                    break;

                case 4:
                    if (isset($this->{$keys[0]}[$keys[1]][$keys[2]][$keys[3]])){
                        return $this->{$keys[0]}[$keys[1]][$keys[2]][$keys[3]];
                    }
                    break;
            }
        }

        return \is_callable($default) ? \call_user_func($default) : $default;

    }, // end of get()

    'getSite' => function() {

        $site = $this->app->module('singletons')->getData($this->siteSingleton, ['lang' => $this->lang]);

        if ($site && \is_array($site)) $this->site = $site;

        return $site;

    }, // end of getSite()

    'getPage' => function($_slug = '') {

        $slugName      = $this->fieldNames['slug'];
        $startpageName = $this->fieldNames['startpage'];
        $publishedName = $this->fieldNames['published'];
        $permalinkName = $this->fieldNames['permalink'];

        $langSuffix    = $this->lang != $this->defaultLang ? '_'.$this->lang : '';

        $route = $this->app['route'];

        $paginationDelim = $this->paginationUriDelimiter;

        // check for /*/page/{int} requests (paginagion)
        $pattern = '/(\/|^)'.\preg_quote($paginationDelim, '/').'\/([0-9]+)$/';

        if (\preg_match($pattern, $_slug, $matches)) {

            $this->app->request->request['page'] = $matches[2];
            $route = \preg_replace($pattern, '', $route);
            $_slug = \preg_replace($pattern, '', $_slug);

        }

        // try to find entry by permalink before starting the whole slug procedure
        if ($this->usePermalinks) {

            $collection = $this->pages;
            $filter = [
                $publishedName => true,
                $permalinkName.$langSuffix => $route,
            ];

            $projection   = null;
            $populate     = false;
            $fieldsFilter = ['lang' => $this->lang];

            foreach ($this->use['collections'] as $collection) {

                $this->app->trigger('multiplane.getpage.permalink.before', [$collection, &$filter, &$projection, &$populate, &$fieldsFilter]);

                $page = $this->app->module('collections')->findOne($collection, $filter, $projection, $populate, $fieldsFilter);

                if ($page) {
                    $this->collection = $collection;
                    $this->_doChecksWithCurrentPage($page);

                    if (!empty($this->preRenderFields) && \is_array($this->preRenderFields)) {
                        $page = $this->renderFields($page);
                    }

                    return $page;
                }
            }

        }

        // extract slug
        $parts = \explode('/', \trim($_slug, '/'));
        $count = \count($parts);
        $slug  = \end($parts);

        $collection = $this->collection;

        if ($count > 1) {

            $parentSlug = $parts[0];

            foreach ($this->structure as $v) {

                if (isset($v['slug'.$langSuffix]) && $v['slug'.$langSuffix] == $parentSlug) {

                    $collection = $v['_id'];
                    $this->collection = $collection;

                    if (isset($v['_pid']) && isset($this->structure[$v['_pid']])) {

                        $parentCollection = $this->structure[$v['_pid']];

                        $filter = [
                            $publishedName         => true,
                            $slugName.$langSuffix  => $parentSlug, // to do: use slug from structure
                            'subpagemodule.active' => true,
                        ];
                        $projection = null;
                        $fieldsFilter = [];

                        if ($parentPage = $this->app->module('collections')->findOne($parentCollection['_id'], $filter, $projection, false, $fieldsFilter)) {
                            $this->parentPage = $parentPage;
                            $this->hasParentPage = true;
                            $this->_addBreadcrumbs($this->parentPage);
                        }
                    }
                    break;
                }
            }

        }

        // startpage
        if (empty($slug)) {

            $this->isStartpage = true;

            $filter = [
                $publishedName => true,
                $startpageName => true,
            ];

        }
        // filter by slug
        else {

            $isLocalized = $this->app->retrieve('unique_slugs/localize/'.$collection, false);
            $_slugName = $slugName == '_id' ? '_id' : $slugName.($isLocalized ? $langSuffix : '');

            $filter = [
                $publishedName => true,
                $_slugName     => $slug,
            ];

        }

        $projection   = null;
        $populate     = false;
        $fieldsFilter = ['lang' => $this->lang];

        $this->app->trigger('multiplane.getpage.before', [$collection, &$filter, &$projection, &$populate, &$fieldsFilter]);

        $page = $this->app->module('collections')->findOne($collection, $filter, $projection, $populate, $fieldsFilter);

        if (!$page) return false;

        // hotfix for localized forms - TODO: cleanup
        // In my test data I had a localized set field of name contactform,
        // which was localized by cockpit's default logic. Without that field,
        // that data doesn't get localized automatically.
        if ($this->isMultilingual && $this->lang != $this->defaultLang) {
            if (isset($page['contactform']) && isset($page['contactform_'.$this->lang])) {
                $page['contactform'] = $page['contactform_'.$this->lang];
                $languages = $this->getLanguages(false, false);
                foreach ($languages as $code) {
                    unset($page['contactform_'.$code]);
                }
            }
        }

        $this->_doChecksWithCurrentPage($page);

        // reroute startpage if called via slug to avoid duplicated content
        $shouldRedirect = \strlen($slug)
            && isset($page[$startpageName]) && $page[$startpageName] === true
            && !$this->app->param('page', false); // posts might be on startpage without slug

        if ($shouldRedirect) {
            $path = '/' . ($this->isMultilingual ? $this->lang : '');
            $url = $this->app->routeUrl($path);
            \header('Location: '.$url, true, 301);
            $this->app->stop();
        }

        if (!empty($this->preRenderFields) && \is_array($this->preRenderFields)) {
            $page = $this->renderFields($page);
        }

        if ($this->hasBackgroundImage) {
            $this->addBackgroundImage($page);
        }

        return $page;

    }, // end of getPage()

    'addBackgroundImage' => function($page = []) {

        $bgImgName = $this->fieldNames['background_image'];

        $background = $page[$bgImgName]['_id']
                   ?? $this->site[$bgImgName]['_id']
                   ?? null;

        if ($background) {

            $css = [];
            $pattern = '';

            foreach ($this->backgroundBreakpoints as $name => $options) {

                $sizes = [];
                foreach ($options['points'] as $o => $size) {
                    $sizes[] = '(' . $o . ':' . $size . 'px)';
                }

                $backgroundSize = $options['size'] ?? $options['points']['max-width'] ?? $options['points']['min-width'] ?? 1920;

                $pattern = '@media ' . implode(' and ', $sizes);

                $css[] = $pattern . '{' . 'html {background-image: url("'.MP_BASE_URL.'/getImage?src='.$background.'&w='.$backgroundSize.'&m=bestFit&q=70");}' . '}';
            }

            $this->add('styles', $css);

        }

    }, // end of addBackgroundImage()

    'getLanguages' => function($extended = false, $withDefault = true) {

        $languages = [];

        if ($this->isMultilingual && \is_array($this->app['languages'])) {

            foreach ($this->app['languages'] as $l => $label) {

                if ($l != 'default' || ($l == 'default' && $withDefault)) {

                    $code = $l == 'default' ? $this->defaultLang : $l;
                    $languages[] = !$extended ? $code : [
                        'code'    => $code,
                        'name'    => $label,
                        'active'  => $code == $this->lang,
                        'default' => $code == $this->defaultLang,
                    ];
                }
            }

        }

        return $languages;

    }, // end of getLanguages()

    /**
     * Init and load i18n file(s),
     * add locale prefix to `base_url`
     *
     * @param string $lang
     * @return void
     */
    'initI18n' => function($lang = 'en') {

        $this->lang = $lang;
        $this->app->helper('i18n')->locale = $lang;

        if ($this->isMultilingual) {
            $this->app->set('base_url', MP_BASE_URL . '/' . $lang);
        }

        // deprecated: custom mp config path
        if ($translationspath = $this->app->path("mp_config:i18n/{$lang}.php")) {
            $this->app->helper('i18n')->load($translationspath, $lang);
        }

        // use babel addon to load specific i18n files
        if (isset($this->app['modules']['babel'])) {
            $this->app->helper('babel')->loadI18n($lang, ['multiplane', 'formvalidation']);
        }

    },

    'getPosts' => function($collection = null, $slug = '', $opts = []) {

        if ($this->pageTypeDetection == 'type') {
            return $this->getPostsByType($collection, $slug, $opts);
        }

        if (!$collection) $collection = $this->posts;

        $collection = $this->app->module('collections')->collection($collection);

        if (!$collection) return false;

        $collectionName = $collection['name'];

        $lang  = $this->lang;
        $page  = $this->app->param('page', 1);
        $limit = (isset($opts['limit']) && (int)$opts['limit'] ? $opts['limit'] : null) ?? $this->displayPostsLimit ?? 5;
        $skip  = ($page - 1) * $limit;

        $filter = [
            $this->fieldNames['published'] => true,
        ];

        $sort = !empty($opts['customsort']) ? $opts['customsort'] : [
            '_created' => isset($opts['sort']) && $opts['sort'] ? 1 : -1,
        ];

        $options = [
            'filter' => $filter,
            'lang'   => $lang,
            'limit'  => $limit,
            'skip'   => $skip,
            'sort'   => $sort,
        ];

        $this->app->trigger('multiplane.getposts.before', [$collectionName, &$options]);

        $posts = $this->app->module('collections')->find($collectionName, $options);

        $count = $this->app->module('collections')->count($collectionName, $options['filter']);

        if (!$posts && $count) {
            // send 404 if no posts found (pagination too high)
            $this->app->response->status = 404;
            return;
        }

        if (!empty($this->preRenderFields) && \is_array($this->preRenderFields)) {
            foreach ($posts as &$post) {
                $post = $this->renderFields($post);
            }
        }

        if ($this->isStartpage) {
            $slug = '';
        }

        $posts_slug = $this->getCollectionSlug($collectionName);

        if ($this->isMultilingual && !$this->usePermalinks) {
            $posts_slug = $this->lang . '/' . $posts_slug;
            $slug       = $this->lang . '/' . $slug;
        }

        $pagination =  [
            'count' => $count,
            'page'  => $page,
            'limit' => $limit,
            'pages' => \ceil($count / $limit),
            'slug'  => $slug,
            'posts_slug'    => $posts_slug,
            'dropdownLimit' => $opts['dropdownLimit'] ?? $this->paginationDropdownLimit ?? 5,
            'hide'  => (!isset($opts['pagination']) || $opts['pagination'] !== true),
        ];

        return compact('posts', 'pagination', 'collection');

    }, // end of getPosts()

    'getPostsByType' => function($type = null, $slug = '', $opts = []) {

        if (!$type) $type = 'post';

        $publishedName = $this->fieldNames['published'];
        $typeName      = $this->fieldNames['type'];

        $lang  = $this->lang;
        $page  = $this->app->param('page', 1);
        $limit = (isset($opts['limit']) && (int)$opts['limit'] ? $opts['limit'] : null)
                  ?? $this->displayPostsLimit ?? 5;
        $skip  = ($page - 1) * $limit;

        $filter = [
            $publishedName => true,
            $typeName      => $type,
        ];

        $options = [
            'filter' => $filter,
            'lang'   => $lang,
            'limit'  => $limit,
            'skip'   => $skip,
        ];

        $posts = $this->app->module('collections')->find($this->pages, $options);

        $count = $this->app->module('collections')->count($this->pages, $filter);

        if (!$posts && $count) {
            // send 404 if no posts found (paginagion too high)
            $this->app->response->status = 404;
            return;
        }

        if (!empty($this->preRenderFields) && \is_array($this->preRenderFields)) {
            foreach($posts as &$post) {
                $post = $this->renderFields($post);
            }
        }

        $posts_slug = '';

        if ($this->isMultilingual && !$this->usePermalinks) {
            $slug = $this->lang . '/' . $slug;
        }

        $pagination =  [
            'count' => $count,
            'page'  => $page,
            'limit' => $limit,
            'pages' => \ceil($count / $limit),
            'slug'  => $slug,
            'posts_slug'    => $posts_slug,
            'dropdownLimit' => $opts['dropdownLimit'] ?? $this->paginationDropdownLimit ?? 5,
            'hide'  => (!isset($opts['pagination']) || $opts['pagination'] !== true),
        ];

        $collection = $this->app->module('collections')->collection($this->pages);

        return compact('posts', 'pagination', 'collection');

    }, // end of getPostsByType()

    '_doChecksWithCurrentPage' => function($page) {

        $slugName      = $this->fieldNames['slug'];
        $startpageName = $this->fieldNames['startpage'];
        $permalinkName = $this->fieldNames['permalink'];
        $bgImgName     = $this->fieldNames['background_image'];

        if ($page[$startpageName] ?? false) $this->isStartpage = true;

        if (isset($page[$bgImgName]) && isset($page[$bgImgName]['_id'])) $this->hasBackgroundImage = true;

        $this->currentSlug = $page[$slugName];

        if ($this->pageTypeDetection == 'type') {
            $this->_checkHasParentPage($page);
        }

    },

    '_checkHasParentPage' => function($page) {

        $slugName      = $this->fieldNames['slug'];
        $startpageName = $this->fieldNames['startpage'];
        $publishedName = $this->fieldNames['published'];
        $permalinkName = $this->fieldNames['permalink'];
        $typeName      = $this->fieldNames['type'];

        if ($this->pageTypeDetection == 'type' && !empty($page[$typeName])) {

            $filter = [
                $publishedName         => true,
                'subpagemodule.active' => true,
                'subpagemodule.type'   => $page[$typeName],
            ];
            $projection   = null;
            $fieldsFilter = [];

            if ($parentPage = $this->app->module('collections')->findOne($this->pages, $filter, $projection, false, $fieldsFilter)) {

                $this->parentPage = $parentPage;
                $this->hasParentPage = true;
                $this->_addBreadcrumbs($this->parentPage);
            }

        }

    },

    '_addBreadcrumbs' => function($page) {

        $slugName      = $this->fieldNames['slug'];
        $titleName     = $this->fieldNames['title'];
        $permalinkName = $this->fieldNames['permalink'];

        $_slugName = $this->usePermalinks ? $permalinkName : $slugName;

        $langSuffix     = $this->lang != $this->defaultLang ? '_'.$this->lang : '';
        $slugLangSuffix = $this->lang != $this->defaultLang && $_slugName != '_id' ? '_'.$this->lang : '';

        $breadcrumbs = $this->breadcrumbs;

        $title = $page[$titleName.$langSuffix];
        $slug  = $page[$_slugName.$slugLangSuffix];

        if ($this->isMultilingual && !$this->usePermalinks) {
            $slug = $this->lang . '/' . $slug;
        }

        $breadcrumbs[] = [
            'title' => $title,
            'slug'  => $slug,
        ];

        $this->breadcrumbs = $breadcrumbs;

    },

    'renderFields' => function($page) {

        $collection = $this->app->module('collections')->collection($this->collection);

        if (!isset($collection['fields'])) return $page;

        $fields = $collection['fields'];

        foreach ($fields as $field) {

            if (!isset($page[$field['name']])) continue;

            if (!\in_array($field['name'], $this->preRenderFields)) continue;

            $cmd  = $field['type'] ?? 'text';
            $opts = $field['options'] ?? [];
            $page[$field['name']] = $this->app->helper('fields')->$cmd($page[$field['name']], $opts);
 
        }

        return $page;

    }, // end of renderFields()

    'accessAllowed' => function() {

        // maintenance mode
        if (!$this->isInMaintenanceMode) return true;

        if (!$ips = $this->allowedIpsInMaintenanceMode) {
            return false;
        }

        else {
            // allow array input or string with white space delimiter
            $ips = \is_array($ips) ? $ips : \explode(' ', \trim($ips));

            if (\in_array($this->app->getClientIp(), $ips)) {
                $this->clientIpIsAllowed = true;
                return true;
            }
        }

        return false;

    }, // end of accessAllowed()

    'loadConfig' => function() {

        // overwrite default config

        // load config
        $config = $this->app->retrieve('multiplane', []);

        if (isset($this->app['modules']['cpmultiplanegui'])
          && isset($config['profile'])
          && $profile = $this->app->module('cpmultiplanegui')->profile($config['profile'])
          ) {
            $config = \array_replace_recursive($config, $profile);
        }

        // load theme config file(s), if available

        if (!empty($config['parentTheme'])) $this->parentTheme = $config['parentTheme'];
        if (!empty($config['theme']))       $this->theme       = $config['theme'];

        $themeConfig = $this->loadThemeConfig();

        if (\is_array($themeConfig)) {
            $config = \array_replace_recursive($themeConfig, $config);
        }

        foreach ($config as $key => $val) {
            if ($key == 'fieldNames') {
                $fieldNames = $this->fieldNames;
                foreach ($val as $fieldName => $replacement) {
                    if (\is_string($replacement) && !empty(\trim($replacement))) {
                        $fieldNames[$fieldName] = \trim($replacement);
                    }
                }
                $this->fieldNames = $fieldNames;
            } else {
                $this->set($key, $val);
            }
        }

        // backwards compatibility checks
        $this->_keepConfigBackwardsCompatible($config);

        // set current collection to pages
        $this->set('collection', $this->pages);

    }, // end of loadConfig()

    'loadThemeConfig' => function() {

        $themeConfig = $parentThemeConfig = [];

        if ($this->themePath = $this->app->path('#themes:'.$this->theme)) {

            if (\file_exists($this->themePath . '/config/config.php')) {
                $themeConfig = include($this->themePath . '/config/config.php');

                if (!$this->parentTheme && !empty($themeConfig['parentTheme'])) {
                    $this->parentTheme = $themeConfig['parentTheme'];
                }
            }

            if ($this->parentTheme) {
                if ($this->parentThemePath = $this->app->path('#themes:'.$this->parentTheme)) {

                    // parent theme path must be set before theme path
                    $this->app->path('theme', $this->parentThemePath);
                    $this->app->path('views', $this->parentThemePath . '/views');

                    if (\file_exists($this->parentThemePath . '/config/config.php')) {
                        $parentThemeConfig = include($this->parentThemePath . '/config/config.php');
                    }
                }
            }

            $this->app->path('theme', $this->themePath);
            $this->app->path('views', $this->themePath . '/views');

            // return theme config
            return \array_replace_recursive($parentThemeConfig, $themeConfig);

        }

        else {
            if (!COCKPIT_CLI && !MP_SELF_EXPORT) {
                echo 'The theme "'.$this->theme.'" doesn\'t exist.';
                $this->app->stop();
            }
        }

    }, // end of loadThemeConfig()

    '_keepConfigBackwardsCompatible' => function($config) {

        // fix slugName
        if (isset($config['slugName']) && !isset($config['fieldNames']['slug'])) {
            $fieldNames = $this->fieldNames;
            $fieldNames['slug'] = $config['slugName'];
            $this->fieldNames = $fieldNames;
        }

        // fix navName
        if (isset($config['navName']) && !isset($config['fieldNames']['nav'])) {
            $fieldNames = $this->fieldNames;
            $fieldNames['nav'] = $config['navName'];
            $this->fieldNames = $fieldNames;
        }

        // fix missing "use"
        if (empty($this->use['collections'])) {
            $collections = [];
            if (!empty($this->pages)) $collections[] = $this->pages;
            if (!empty($this->posts)) $collections[] = $this->posts;
            $use = $this->use;
            $use['collections'] = $collections;
            $this->use = $use;
        }

        // fix missing structure
        if (empty($this->structure)) {

            $structure = [];
            $languages = $this->getLanguages(false, false);

            $slugName      = $this->fieldNames['slug'];
            $publishedName = $this->fieldNames['published'];

            foreach ($this->use['collections'] as $col) {

                $_collection = $this->app->module('collections')->collection($col);

                if ($col == $this->pages) {
                    $structure[$col] = [
                        '_id'        => $_collection['name'],
                        'label'      => $_collection['label'] ?? $_collection['name'],
                        'slug'       => '',
                    ];
                    foreach ($languages as $l) {
                        $structure[$col]['slug_'.$l] = '';
                    }
                    continue;
                }

                $filter = [
                    $publishedName => true,
                    'subpagemodule.active'     => true,
                    'subpagemodule.collection' => $col,
                ];
                $tmp = $this->app->module('collections')->findOne($this->pages, $filter, null, false, null);

                if ($tmp) {

                    if ($slugName == '_id') {
                        $slug = $tmp['_id'];
                    } else {
                        $slug = !empty($tmp['subpagemodule']['route'])
                                ? $tmp['subpagemodule']['route']
                                : $tmp[$slugName];
                        $slug = ltrim($slug, '/');
                    }

                    $structure[$col] = [
                        '_id'        => $_collection['name'],
                        'label'      => $_collection['label'] ?? $_collection['name'],
                        'slug'       => $slug,
                        '_pid'       => $this->pages,
                    ];
                    foreach ($languages as $l) {
                        if ($slugName == '_id') {
                            $slug = $tmp['_id'];
                        } else {
                            $slug = (!empty($tmp['subpagemodule']['route_'.$l])) ? $tmp['subpagemodule']['route_'.$l] : $tmp[$slugName.'_'.$l];
                            $slug = ltrim($slug, '/');
                        }
                        $structure[$col]['slug_'.$l] = $slug;
                    }

                }

            }

            $this->structure = $structure;
        }

    }, // end of _keepConfigBackwardsCompatible()

    'extendLexyTemplateParser' => function() {

        // create image url shortcuts

        if (empty($this->lexy) || !\is_array($this->lexy)) return;

        foreach ($this->lexy as $k => $v) {

            $pattern = '/(\s*)@'.\preg_quote($k, '/').'\((.+?)\)/';

            $replacement = '$1<?php echo $app->module(\'multiplane\')->imageUrl($2, \''.$k.'\');?>';

            $this->app->renderer->extend(function($content) use ($pattern, $replacement) {
                return \preg_replace($pattern, $replacement, $content);
            });

        }

    }, // end of extendLexyTemplateParser()

    'self_export' => function() {

        $constants = [
            'MP_ENV_ROOT'     => MP_ENV_ROOT,
            'MP_BASE_URL'     => MP_BASE_URL,
            'MP_ADMIN_FOLDER' => MP_ADMINFOLDER,
            'MP_ENV_URL'      => MP_ENV_URL, // wrong url guess, if called from `/cockpit`
        ];

        $theme = [
            'name'        => $this->theme,
            'path'        => $this->themePath,
            'parentTheme' => $this->parentTheme,
            // 'config'      => $this->loadThemeConfig(),
        ];

        $themes = [];

        foreach ($this->app->paths('#themes') as $themesDir) {

            foreach ($this->app->helper('fs')->ls($themesDir) as $dir) {

                if (!$dir->isDir()) continue;

                $name = $dir->getFileName();
                $path = $dir->getPathName();

                if (isset($themes[$name])) continue;

                $config = [];
                $info   = [];

                if (\file_exists("{$path}/config/config.php")) {
                    $config = include("{$path}/config/config.php");
                }
                if (\file_exists("{$path}/package.json")) {
                    $json = $this->app->helper('fs')->read("{$path}/package.json");
                    $info = \json_decode($json, true);
                }

                $theme = [
                    'name'   => $name,
                    'path'   => $path,
                    'image'  => '',
                    'config' => $config,
                    'info'   => $info,
                ];

                $extensions = ['png', 'jpg'];
                foreach ($extensions as $ext) {
                    if ($image = $this->app->pathToUrl("{$path}/screenshot.{$ext}")) {
                        $theme['image'] = $image; break;
                    }
                }

                $themes[$name] = $theme;
            }

        }

        return compact('constants', 'theme', 'themes');

    }, // end of self_export()

    'generateToken' => function() {

        return \uniqid(\bin2hex(\random_bytes(16)));

    },

    'getCollectionSlug' => function($collection) {

        $langSuffix = $this->getLanguageSuffix();

        return $this->structure[$collection]['slug'.$langSuffix];

    },

    'getLanguageSuffix' => function() {

        return $this->lang == $this->defaultLang ? '' : '_'.$this->lang;

    },

    /**
     * @param string|array $src
     * @param string $profile
     * @return string
     */
    'imageUrl' => function($src, $profile = '') {

        $asset = null;

        if (\is_array($src)) {
            $asset = $src;
        }
        elseif (\is_string($src)) {

            // lazy check if path or id
            $isId = \strpos($src, '.') === false;

            if ($isId) {
                $asset = $this->app->storage->findOne('cockpit/assets', ['_id'  => $src]);
            }
            else {
                $src   = \str_replace('../', '', \rawurldecode($src));

                // remove `/storage/uploads` from image urls
                $uploads = \str_replace(COCKPIT_ENV_ROOT, '', $this->app->path('#uploads:'));
                if (\strpos($src, $uploads) === 0) {
                    $src = '/'.\substr($src, \strlen($uploads));
                }

                $asset = $this->app->storage->findOne('cockpit/assets', ['path' => $src]);
            }

            if (!$asset) {
                return !$isId ? $src : '';
            }

        }

        if (!empty($profile)) {

            if ($asset) {

                $isSVG = \preg_match('/svg/', $asset['mime']);

                if (isset($asset['sizes'][$profile]['path'])) {
                    $path = $asset['sizes'][$profile]['path'];
                }
                elseif ($isSVG || (isset($this->lexy[$profile]) && $this->lexy[$profile] === 'raw')) {
                    $path = $asset['path'];
                }
                else {
                    // fallback to getImage route to create thumbs on the fly
                    return $this->getImageUrl($asset['_id'], $profile);
                }

                $url = $this->app->pathToUrl('#uploads:'.\ltrim($path,'/'))
                    ?? $this->app->filestorage->getUrl('assets://') . $path;

                return $url;
            }
        }

        return '';

    }, // end of imageUrl

    /**
     * @param string $src
     * @param string $profile
     * @return string
     */
    'getImageUrl' => function($src, $profile) {

        $options = $this->lexy[$profile] ?? false;

        if (!$options || !\is_string($src)) return '';

        // @uploads
        if (\is_string($options) && $options === 'raw') {
            return $this->app->pathToUrl("#uploads:{$src}");
        }

        // @thumbnail...
        $url = $this->app->routeUrl('/getImage') . '?src='.$src;

        $map = [
            'width'   => 'w',
            'height'  => 'h',
            'quality' => 'q',
            'method'  => 'm'
        ];

        foreach ($options as $k => $v) {
            $url .= isset($map[$k]) ? "&{$map[$k]}={$v}" : '';
        }

        return $url;

    }, // end of getImageUrl()

]);

// module parts
include_once(__DIR__ . '/module/template-helpers.php');
include_once(__DIR__ . '/module/forms.php');

// experimental parts
include_once(__DIR__ . '/experimental/sitemap.php');
include_once(__DIR__ . '/experimental/matomo.php');
include_once(__DIR__ . '/experimental/seo.php');

$this->on('multiplane.init', function() {

    if (!MP_SELF_EXPORT) {
        require_once(__DIR__ . '/override.php');
    }

    // overwrite default config
    $this->module('multiplane')->loadConfig();

    // load theme bootstrap file(s)
    if ($this->module('multiplane')->parentTheme && $this->module('multiplane')->parentThemeBootstrap
        && \file_exists($this->module('multiplane')->parentThemePath . '/bootstrap.php')) {

        include_once($this->module('multiplane')->parentThemePath . '/bootstrap.php');
    }
    if (\file_exists($this->module('multiplane')->themePath . '/bootstrap.php')) {
        include_once($this->module('multiplane')->themePath . '/bootstrap.php');
    }

    // load custom bootstrap file
    if (\file_exists(MP_CONFIG_DIR.'/bootstrap.php')) {
        include_once(MP_CONFIG_DIR.'/bootstrap.php');
    }

    // extend lexy parser for custom image url templating
    $this->module('multiplane')->extendLexyTemplateParser();

    // bind routes

    if (!MP_SELF_EXPORT) {

        // skip binding routes if in maintenance mode and
        // don't bind any routes, if users wants to use only their own routes
        if ($this->module('multiplane')->accessAllowed()
        && !$this->module('multiplane')->disableDefaultRoutes) {
            require_once(__DIR__ . '/default-routes.php');
        }

    }

}, 9999);

// CLI
if (COCKPIT_CLI) {
    $this->path('#cli', __DIR__.'/cli');
}
