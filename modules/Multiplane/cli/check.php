<?php
/**
 * This is a modified version of
 * https://github.com/agentejo/cockpit/blob/next/install/index.php
 * author of original file: Artur Heinze, http://agentejo.com, MIT License
 */

if (!COCKPIT_CLI) return;

define('COCKPIT_INSTALL', true);

$sqlitesupport = false;

// check whether sqlite is supported
try {

    if (extension_loaded('pdo')) {
        $test = new PDO('sqlite::memory:');
        $sqlitesupport = true;
    }

} catch (Exception $e) { }

function ensure_writable($path) {
    try {
        $dir = COCKPIT_STORAGE_FOLDER.$path;
        if (!file_exists($dir)) {
            mkdir($dir, 0700, true);
            if ($path === '/data') {
                if (file_put_contents($dir.'/.htaccess', 'deny from all') === false) {
                    return false;
                }
            }
        }
        return is_writable($dir);
    } catch (Exception $e) {
        error_log($e);
        return false;
    }
}

// misc checks
$checks = array(
    'Php version >= 7.1.0'                              => (version_compare(PHP_VERSION, '7.1.0') >= 0),
    'Missing PDO extension with Sqlite support'         => $sqlitesupport,
    'GD extension not available'                        => extension_loaded('gd'),
    'MBString extension not available'                  => extension_loaded('mbstring'),
    'Data folder is not writable: /storage/data'        => ensure_writable('/data'),
    'Cache folder is not writable: /storage/cache'      => ensure_writable('/cache'),
    'Temp folder is not writable: /storage/tmp'         => ensure_writable('/tmp'),
    'Thumbs folder is not writable: /storage/thumbs'    => ensure_writable('/thumbs'),
    'Uploads folder is not writable: /storage/uploads'  => ensure_writable('/uploads'),
);

$failed = [];

foreach ($checks as $info => $check) {

    if (!$check) {
        $failed[] = $info;
    }
}

if (count($failed)) {

    foreach ($failed as $info) {
        CLI::writeln($info, false);
    }
    exit(1);
}
else {
    CLI::writeln('No problems found', true);
    exit(0);
}
