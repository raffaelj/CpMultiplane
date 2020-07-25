<?php
/**
 * Copy template files from theme
 * 
 * Usage: `./mp multiplane/copy-templates`
 *
 */

if (!COCKPIT_CLI) return;


$theme      = $app->param('theme', 'rljbase');
$template   = $app->param('template', 'minimal');
$config     = $app->param('config', false);

$fs = $app->helper('fs');

if (!$config) {
    // get template config
    $themePath = $app->path(MP_ENV_ROOT.'/themes/'.$theme);
    if (!$themePath) $themePath = $app->path("multiplane:themes/$theme");
    if ($templateConfigPath = $app->path("$themePath/templates/$template/template.php")) {
        $config = include($templateConfigPath);
    } else {
        return CLI::writeln("Couldn't find template config file (theme: $theme, template: $template)", false);
    }
}

foreach ($config['copy'] as $copy) {

    if ($source = $app->path($copy['source'])) {

        $name = \basename($source);
        $dest = $copy['destination'];

        // create folder if it doesn't exist, e. g.: '#config:', otherwise the copy command could fail
        if (\is_dir($source)) {
            $path = $dest;
            if (\strpos($path, ':') !== false && !$app->path($dest)) {
                list($namespace, $additional) = \explode(":", $path, 2);
                if (isset($app->paths[$namespace])) {
                    if ($fs->mkdir($app->paths($namespace)[0])) CLI::writeln("Created folder $namespace", true);
                    else CLI::writeln("Could not create folder $namespace", false);
                }
                $dest = $app->path("{$namespace}:").$additional;
            }
        }

        if ($fs->copy($source, $dest)) CLI::writeln("Copied $name", true);
        else CLI::writeln("Could not copy $name", false);

    }
}
