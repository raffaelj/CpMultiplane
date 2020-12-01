<?php
/**
 * Download i18n files
 *
 * @todo:
 * [x] cockpit i18n
 * [ ] multiplane i18n
 * [x] tinyMCE i18n
 *
 * Usage: `./mp multiplane/load-i18n`
 */

if (!COCKPIT_CLI) return; 

$reload = $app->param('reload', false);

$languages      = $app->module('multiplane')->getLanguages(true);
$isMultilingual = $app->module('multiplane')->isMultilingual;
$lang           = $app->module('multiplane')->lang;

$fs    = $app->helper('fs');
$error = false;

if ($lang == 'en' && !$isMultilingual) {
    return CLI::writeln("Nothing to do here", true);
}

// try to download Cockpit i18n file
// https://raw.githubusercontent.com/agentejo/cockpit-i18n/master/de.php
foreach ($languages as $l) {

    $code = $l['code'];

    if ($code == 'en') continue;

    $url  = "https://raw.githubusercontent.com/agentejo/cockpit-i18n/master/{$code}.php";
    $dest = $app->path("#config:") . "cockpit/i18n/{$code}.php";

    // skip download if lang file exists
    if (!$reload && $app->path("#config:cockpit/i18n/{$code}.php")) continue;

    if (!$fs->write("{$dest}", $handle = @fopen($url, 'r'))) {
        $error = "Couldn't download {$url}!";
    }
    @fclose($handle);

    if ($error) {
        return CLI::writeln($error, false);
    } else {
        CLI::writeln("Downloaded language file to '#config:cockpit/i18n/{$code}.php", true);
    }

}

// try to download mp i18n file
// https://raw.githubusercontent.com/raffaelj/CpMultiplane-i18n/master/de.php
if (!$app->path("mp_config:")) {
    $fs->mkdir($app->paths('mp_config')[0]);
}

foreach ($languages as $l) {

    $code = $l['code'];

    if ($code == 'en') continue;

    $url  = "https://raw.githubusercontent.com/raffaelj/CpMultiplane-i18n/master/{$code}.php";
    $dest = $app->path("mp_config:") . "i18n/{$code}.php";

    // skip download if lang file exists
    if (!$reload && $app->path("mp_config:i18n/{$code}.php")) continue;

    if (!$fs->write("{$dest}", $handle = @fopen($url, 'r'))) {
        $error = "Couldn't download {$url}!";
    }
    @fclose($handle);

    if ($error) {
        return CLI::writeln($error, false);
    } else {
        CLI::writeln("Downloaded language file to 'mp_config:i18n/{$code}.php", true);
    }

}

// try to download tinyMCE lang file
// url schema: https://www.tiny.cloud/tinymce-services-azure/1/i18n/download?langs=fr_FR,de
// docs: https://www.tiny.cloud/docs-4x/configure/localization/#language
// manual download: https://www.tiny.cloud/get-tiny/language-packages/

$tmppath = $app->path('#tmp:');
$zipname = null;
$error   = false;

// Feel free to send a pull request with a complete list of code mappings
$codes = [
    'de' => 'de',
    'fr' => 'fr_FR',
];

$langs = [];
foreach ($languages as $l) {

    $code = $l['code'];

    if ($code == 'en') continue;

    $langs[] = $codes[$code] ?? $code;

}

if (empty($langs)) return;

$url = 'https://www.tiny.cloud/tinymce-services-azure/1/i18n/download?langs=' . implode(',', $langs);

$filename = 'tinymce_languages.zip';
$zipname  = 'tinymce_languages';

// skip download if lang file exists
if (!$app->path("{$tmppath}/{$filename}") || $reload) {
    if (!$fs->write("{$tmppath}/{$filename}", $handle = @fopen($url, 'r'))) {
        $error = "Couldn't download {$url}!";
    }
    @fclose($handle);

    if ($error) {
        return CLI::writeln($error, false);
    } else {
        CLI::writeln("Downloaded {$filename} to '#tmp:'.", true);
    }
}

$fs->mkdir("{$tmppath}/{$zipname}");
$fs->mkdir("#storage:assets/cockpit/i18n/tinymce");

$dest = $app->path("#storage:assets/cockpit/i18n/tinymce");
$zip  = new \ZipArchive;

if ($zip->open("{$tmppath}/{$filename}") === true) {

    if (!$zip->extractTo("{$tmppath}/{$zipname}")) {
        $error = 'Extracting zip file failed!';
    }
    $zip->close();
} else {
    $error = 'Open zip file failed!';
}

if ($error) {
    return CLI::writeln($error, false);
} else {
    CLI::writeln("Extracted {$filename} to '#tmp:{$zipname}'.", true);
}

foreach ($fs->ls('*.js', "{$tmppath}/{$zipname}/langs") as $file) {
    $name = $file->getFileName();
    $code = (explode('.', $name))[0];

    foreach ($codes as $k => $v) {
        if ($v == $code) {
            $code = $k;
            break;
        }
    }

    if ($fs->copy($file->getRealPath(), "{$dest}/{$code}.js", false)) {
        CLI::writeln("Downloaded tinyMCE language file to '{$dest}/{$code}.js'", true);
    } else {
        CLI::writeln("Couldn't write tinyMCE language file.", false);
    }

}

// cleanup
$fs->delete("$tmppath/$filename");
$fs->delete("$tmppath/$zipname");

CLI::writeln('I18n loading is done.');
