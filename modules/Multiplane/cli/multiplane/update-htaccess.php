<?php
/**
 * Update .htaccess or create .htaccess from dist file
 *
 * Usage: `./mp multiplane/update-htaccess`
 *
 */

if (!COCKPIT_CLI) return;

CLI::writeln("Start to update .htaccess file...");

$fallbackUrl = 'https://raw.githubusercontent.com/raffaelj/CpMultiplane/master/.htaccess.dist';

$isUpdate       = \file_exists(MP_DOCS_ROOT.'/.htaccess');
$distFileExists = \file_exists(MP_DOCS_ROOT.'/.htaccess.dist');

if (!$distFileExists) {
    CLI::writeln("Couldn't find .htaccess.dist - start download...");
    $download = \file_get_contents($fallbackUrl);
    if (!$download) {
        CLI::writeln("Couldn't download $fallbackUrl", false);
        return;
    }
    \file_put_contents(MP_DOCS_ROOT.'/.htaccess.dist', $download);
}

if (!$isUpdate) {
    \copy(MP_DOCS_ROOT.'/.htaccess.dist', MP_DOCS_ROOT.'/.htaccess');
    CLI::writeln('Created .htaccess from dist file.', true);
    return;
}

$currentFile = \file_get_contents(MP_DOCS_ROOT.'/.htaccess');
$distFile    = \file_get_contents(MP_DOCS_ROOT.'/.htaccess.dist');

// replace dist part, but keep user changes above/below
$pattern = '/^# BEGIN CpMultiplane.*# END CpMultiplane$/sm';

\preg_match($pattern, $distFile, $matches);
if (!isset($matches[0])) {
    CLI::writeln("Invalid .htaccess.dist file. Update skipped.", false);
    return;
} else {
    $replacement = $matches[0];
}

\preg_match($pattern, $currentFile, $matches);
if (!isset($matches[0])) {
    CLI::writeln("Invalid .htaccess file. Update skipped.", false);
    return;
} else {
    if ($replacement == $matches[0]) {
        CLI::writeln("Nothing to change here. Update skipped.");
        return;
    }
}

$new = \preg_replace($pattern, $replacement, $currentFile);

\file_put_contents(MP_DOCS_ROOT.'/.htaccess', $new);

CLI::writeln("Updated .htaccess", true);
