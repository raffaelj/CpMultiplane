<?php
/**
 * Create a new preview token and enable live preview in all collections, that are in use
 *
 * Usage: `./mp multiplane/enable-preview`
 *
 */

if (!COCKPIT_CLI) return;

$newToken = mp()->generateToken();
$currentProfile = mp()->profile;
$use = mp()->use;

CLI::writeln("Enabling live preview in current profile $currentProfile");

$app->module('cpmultiplanegui')->updateProfile($currentProfile, [
    'isPreviewEnabled' => true,
    'livePreviewToken' => $newToken
]);

if (!empty($use['collections'])) {
    foreach ($use['collections'] as $col) {
        $app->module('collections')->updateCollection($col, [
            'contentpreview' => [
                'enabled' => true,
                'url' => 'root://livePreview?token='.$newToken,
            ]
        ]);
        CLI::writeln("Enabled live preview in $col", true);
    }
} else {
    CLI::writeln("No collections in use, that could be updated.", false);
}
