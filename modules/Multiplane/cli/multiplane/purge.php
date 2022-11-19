<?php
/**
 * Caution: You will lose all your data!
 *
 * This cli command is meant to revert everything from the quickstart command.
 *
 * Usage: `./mp multiplane/purge`
 *
 */

if (!COCKPIT_CLI) return;

$keepAddons = $app->param('keep-addons', false);

CLI::writeln("Start to purge user data");

$paths = [
    '#config:',
    '#storage:assets',
    '#storage:collections',
    '#storage:editorformats',
    '#storage:forms',
    '#storage:multiplane',
    '#storage:singleton',
    '#data:cockpit.sqlite',
    '#data:cockpit.memory.sqlite',
    '#data:collections.sqlite',
    '#data:cockpitdb.sqlite',
    '#data:forms.sqlite',
];

if (!$keepAddons) {
    $paths = array_merge($paths, [
        '#addons:CpMultiplaneGUI',
        '#addons:FormValidation',
        '#addons:ImageResize',
        '#addons:rljUtils',
        '#addons:SimpleImageFixBlackBackgrounds',
        '#addons:UniqueSlugs',
        '#addons:VideoLinkField',
        '#addons:EditorFormats',
    ]);
}

foreach ($paths as $path) {
    $app->helper('fs')->delete($path);
}

$app->module('cockpit')->clearCache();

CLI::writeln("Purged user data", true);
