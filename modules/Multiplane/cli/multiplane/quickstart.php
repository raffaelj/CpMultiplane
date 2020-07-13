<?php
/**
 * Setup CpMultiplane with predefined addons, collections etc.
 *
 * To do:
 * [x] create default account
 * [x] install addons
 * [x] create default collections
 * [x] create default singleton
 * [x] create default form
 * [x] create default profile
 * [x] copy .htaccess
 * [ ] copy .env
 * [x] generate live preview token
 * [ ] tinymce i18n
 * [ ] i18n
 * [ ] 
 *
 */

if (!COCKPIT_CLI) return;

function run_commands($commands) {

    global $app;

    foreach ($commands as $command) {

        $cmd = $command['cmd'];

        $script = $app->path("#config:cli/{$cmd}.php");

        if (!$script) $script = $app->path("#cli:{$cmd}.php");

        if ($script) {

            foreach ($command['args'] ?? [] as $k => $v) {
                $app->request->request[$k] = $v;
            }

            include($script);

            foreach ($command['args'] ?? [] as $k => $v) {
                $app->request->request[$k] = null;
            }

        } else {
            CLI::writeln("Error: Command \"{$cmd}\" not found!", false);
        }
    }
}

if (!$app->path(MP_DOCS_ROOT . '/.htaccess')) {
    $app->helper('fs')->copy(MP_DOCS_ROOT . '/.htaccess.dist', MP_DOCS_ROOT . '/.htaccess');
    CLI::writeln('Created .htaccess dist file.');
}

$commands = [
    [
        'cmd'  => 'account/create',
        'args' => [
            'user'     => $app->param('user', 'admin'),
            'email'    => $app->param('email', 'admin@yourdomain.de'),
            'password' => $app->param('password', 'admin'),
        ]
    ],
    [
        'cmd' => 'multiplane/copy-templates',
    ]
];

$addons = [
    'CpMultiplaneGUI' => 'https://github.com/raffaelj/cockpit_CpMultiplaneGUI/archive/master.zip',
    'FormValidation'  => 'https://github.com/raffaelj/cockpit_FormValidation/archive/master.zip',
    'ImageResize'     => 'https://github.com/raffaelj/cockpit_ImageResize/archive/master.zip',
    'rljUtils'        => 'https://github.com/raffaelj/cockpit_rljUtils/archive/master.zip',
    'SimpleImageFixBlackBackgrounds' => 'https://github.com/raffaelj/cockpit_SimpleImageFixBlackBackgrounds/archive/master.zip',
    'UniqueSlugs'     => 'https://github.com/raffaelj/cockpit_UniqueSlugs/archive/master.zip',
    'VideoLinkField'  => 'https://github.com/raffaelj/cockpit_VideoLinkField/archive/master.zip',
    'EditorFormats'   => 'https://github.com/pauloamgomes/CockpitCms-EditorFormats/archive/master.zip',
];

foreach ($addons as $name => $url) {
    $commands[] = [
        'cmd'  => 'install/addon',
        'args' => ['name' => $name, 'url' => $url]
    ];
}

run_commands($commands);

// reload config
CLI::writeln("Reload config...");

$app->loadModules([$app->path('#addons:')]);
$config = include($app->path('#config:config.php'));
$app->set('multiplane', $config['multiplane'] ?? []);
mp()->loadConfig();

// must be called after CpMultiplaneGUI is installed
$commands = [
    [
        'cmd' => 'multiplane/enable-preview',
    ]
];

run_commands($commands);
