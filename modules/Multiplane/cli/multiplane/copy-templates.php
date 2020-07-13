<?php
/**
 * If your theme has a templates folder, all files inside it will be copied
 * into your cockpit installation
 * 
 * Usage: `./mp multiplane/copy-templates`
 *
 */

if (!COCKPIT_CLI) return;

// there might be a wrong theme configuration already
if (!mp()->themePath) {
    mp()->set('theme', 'rljbase');
    mp()->loadThemeConfig();
}

if ($templatesPath = $app->path(mp()->themePath . '/templates')) {

    if (\is_dir($templatesPath.'/config')) {

        CLI::writeln("Copy config folder");

        // create config folder if it doesn't exist
        if (!$app->path('#config:')) $app->helper('fs')->mkdir($app->paths('#config')[0]);

        $app->helper('fs')->copy($templatesPath.'/config', '#config:', true);
    }

    if (\is_dir($templatesPath.'/storage')) {

        CLI::writeln("Copy storage folder");
        $app->helper('fs')->copy($templatesPath.'/storage', '#storage:', true);
    }

} else {
    CLI::writeln("Your theme {$mp()->theme} has no templates folder", false);
}
