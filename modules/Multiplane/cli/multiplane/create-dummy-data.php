<?php
/**
 * Create dummy pages and posts from installed addons
 * 
 * Usage: `./mp multiplane/create-dummy-data`
 *
 */

if (!COCKPIT_CLI) return;

$pagesCollection = $app->param('pages', 'pages');   // name of pages collection
$postsCollection = $app->param('posts', 'posts');   // name of posts collection
$siteSingleton   = $app->param('site', 'site');     // name of site singleton
$logo            = $app->param('logo', false);      // path to logo
$site_name       = $app->param('site_name', 'CpMultiplane');
$description     = $app->param('description', 'A small php frontend for Cockpit CMS');

$pageTypeDetection = $app->module('multiplane')->pageTypeDetection;

if ($pageTypeDetection == 'type') $postsCollection = $pagesCollection;

$_pagesCollection = $app->module('collections')->collection($pagesCollection);
$_postsCollection = $app->module('collections')->collection($postsCollection);

if (!$_pagesCollection) {
    return CLI::writeln("$pagesCollection collection doesn't exist.", false);
}
if (!$_postsCollection) {
    return CLI::writeln("$postsCollection collection doesn't exist.", false);
}
if (!$app->module('singletons')->exists($siteSingleton)) {
    return CLI::writeln("$siteSingleton singleton doesn't exist.", false);
}

// add MP logo as asset
$file = $logo ? $app->path($logo) : $app->path('cpmultiplanegui:icon.svg');
$logo = [];
if ($file) {
    $meta = ['title' => 'CpMultiplane logo'];
    $assets = $app->module('cockpit')->saveAssets([$file], $meta);
    $logo = isset($assets[0]) ? $assets[0] : [];
}

// create dummy site config
$app->module('singletons')->saveData($siteSingleton, [
    'site_name'   => $site_name,
    'description' => $description,
    'logo'        => $logo,
]);

foreach ($_pagesCollection['fields'] ?? [] as $field) {
    if ($field['name'] == 'content') {
        $pagesContentType = $field['type'];
        break;
    }
}
foreach ($_postsCollection['fields'] ?? [] as $field) {
    if ($field['name'] == 'content') {
        $postsContentType = $field['type'];
        break;
    }
}

function contentStringToRepeater($str) {
    return [
        [
            'field' => [
                'type' => 'wysiwyg',
                'label' => 'Wysiwyg',
                'options' => [
                    'editor' => [
                        'format' => 'Advanced',
                    ],
                ],
            ],
            'value' => $str,
        ],
    ];
}

// create dummy pages
$pages = [];
$posts = [];

// create parent page for addons
$entry = [
    'title' => 'Addons',
    'content' => '<p>List of addons</p>',
    'published' => true,
    'nav' => ['main'],
    'subpagemodule' => [
        'active' => true,
        'collection' => $postsCollection,
        'pagination' => true,
        'route' => 'addons',
    ],
    '_o' => 1,
];
if ($pageTypeDetection == 'type') {
    $entry['subpagemodule']['type'] = 'post';
    $entry['type'] = 'page';
    unset($entry['subpagemodule']['collection']);
}
// check, if content is a repeater
if ($pagesContentType == 'repeater') {
    $entry['content'] = contentStringToRepeater($entry['content']);
}

$addonsPage = $app->module('collections')->save($pagesCollection, $entry);

// create contact page
if (isset($app['modules']['formvalidation']) && $form = $app->module('forms')->form('contact')) {
    $entry = [
        'title' => 'Contact',
        'content' => '<p>Send me a message</p>',
        'published' => true,
        'nav' => ['main'],
        '_o' => 2,
        'contactform' => [
            'active' => true,
            'form' => 'contact',
        ],
    ];

    // check, if content is a repeater
    if ($pagesContentType == 'repeater') {
        $entry['content'] = contentStringToRepeater($entry['content']);
    }
    if ($pageTypeDetection == 'type') $entry['type'] = 'page';

    $pages[] = $entry;
}

foreach (array_keys((array) $app['modules']) as $module) {

    if (in_array($module, ['cockpit', 'collections', 'singletons', 'forms'])) continue;

    $isPost = $module != 'multiplane';

    $entry = [
        'published' => true,
        'nav' => ['main'],
    ];
    if ($pageTypeDetection == 'type') $entry['type'] = 'post';

    $dir = $app->module($module)->_dir;
    $title = basename($dir);
    $entry['title'] = $title;

    $readme = $app->path("$module:README.md");
    if (!$readme) $readme = $app->path("$module:README.MD");
    if (!$readme) $readme = $app->path("$module:readme.md");
    if (!$readme) $readme = $app->path("$module:README.TXT");
    if (!$readme) $readme = $app->path("$module:readme.txt");

    if ($module == 'multiplane') {
        $entry['startpage'] = true;
        unset($entry['nav']);
        if ($pageTypeDetection == 'type') $entry['type'] = 'page';
        $entry['_o'] = 0;
        $readme = $app->path(MP_DOCS_ROOT . '/README.md');
    }

    if ($readme) {
        $content = $app->helper('fs')->read($readme);

        // do some conversion to match the headline structure
        // md h1 --> title, h{2,3,4,5} --> h{3,4,5,6}, h6 --> <b>...</b>
        // works only for md headlines starting with '#', but hey, this is just dummy data
        $parts = explode("\n", $content, 2);
        if (strpos($parts[0], '# ') === 0) {
            $entry['title'] = substr($parts[0], 2);
        }
        $content = preg_replace('/^(#{6}) (.*)/m', '**$2**', $parts[1]);
        $content = preg_replace('/^(#{1,6}) (.*)/m', '#$1 $2', $content);

        $entry['content'] = $app->module('cockpit')->markdown($content, true);

        // select first line as excerpt
        preg_match('/(?<!\h)^(.+)$/m', $entry['content'], $matches);
        $excerpt = $matches[0];

        // check, if content is a repeater
        if (  (!$isPost && $pagesContentType == 'repeater')
            || ($isPost && $postsContentType == 'repeater') ) {
            $entry['content'] = contentStringToRepeater($entry['content']);
        }
    }

    if ($isPost) {
        $entry['excerpt'] = $excerpt;
        $entry['tags'] = ['addon', 'cockpit', $module];
        if ($pageTypeDetection == 'type') $entry['_pid'] = $addonsPage['_id'];
    }

    if ($isPost) $posts[] = $entry;
    else         $pages[] = $entry;

}

if ($app->module('collections')->save($pagesCollection, $pages)) CLI::writeln("Created dummy pages in $pagesCollection", true);
else CLI::writeln("Failed to create dummy pages in $pagesCollection", false);

if ($app->module('collections')->save($postsCollection, $posts)) CLI::writeln("Created dummy posts in $postsCollection", true);
else CLI::writeln("Failed to create dummy posts in $postsCollection", false);
