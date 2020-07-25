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
 * [x] i18n --> mp multiplane/load-i18n
 * [x] create dummy data --> mp multiplane/create-dummy-data
 *
 */

if (!COCKPIT_CLI) return;

$user       = $app->param('user',       'admin');
$email      = $app->param('email',      'admin@yourdomain.de');
$password   = $app->param('password',   'admin');
$theme      = $app->param('theme',      'rljbase');
$template   = $app->param('template',   'minimal');
$i18n       = $app->param('i18n',       'en');
$languages  = $app->param('languages',  false);

// copy .htaccess from dist file
if (!$app->path(MP_DOCS_ROOT . '/.htaccess')) {
    $app->helper('fs')->copy(MP_DOCS_ROOT . '/.htaccess.dist', MP_DOCS_ROOT . '/.htaccess');
    CLI::writeln('Created .htaccess from dist file.');
}

// get template config
$themePath = $app->path(MP_ENV_ROOT.'/themes/'.$theme);
if (!$themePath) $themePath = $app->path("multiplane:themes/$theme");
if ($templateConfigPath = $app->path("$themePath/templates/$template/template.php")) {
    $config = include($templateConfigPath);
} else {
    return CLI::writeln("Couldn't find template config file (theme: $theme, template: $template)", false);
}

// batch execute multiple cli commands
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

$commands = [
    [
        'cmd'  => 'account/create',
        'args' => [
            'user'     => $user,
            'email'    => $email,
            'password' => $password,
        ]
    ],
    [
        'cmd' => 'multiplane/copy-templates',
        'args' => ['config' => $config]
    ]
];

// install addons
$addons = $config['addons'];
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
$app->module('multiplane')->loadConfig();

// must be called after CpMultiplaneGUI is installed
$commands = [
    [
        'cmd' => 'multiplane/enable-preview',
    ],
];

run_commands($commands);

CLI::writeln("Quickstart is done. Now login and create content.");

CLI::writeln("To download i18n files automatically run:
./mp multiplane/load-i18n");

CLI::writeln("To create some dummy data run:
./mp multiplane/create-dummy-data");
