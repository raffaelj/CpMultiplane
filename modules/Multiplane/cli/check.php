<?php
/**
 * This is a modified version of
 * https://github.com/agentejo/cockpit/blob/next/install/index.php
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

// misc checks
$checks = array(
    'Php version >= 7.1.0'                              => (version_compare(PHP_VERSION, '7.1.0') >= 0),
    'Missing PDO extension with Sqlite support'         => $sqlitesupport,
    'GD extension not available'                        => extension_loaded('gd'),
    'MBString extension not available'                  => extension_loaded('mbstring'),
    'Data  folder is not writable: /storage/data'       => is_writable(COCKPIT_STORAGE_FOLDER.'/data'),
    'Cache folder is not writable: /storage/cache'      => is_writable(COCKPIT_STORAGE_FOLDER.'/cache'),
    'Temp folder is not writable: /storage/tmp'         => is_writable(COCKPIT_STORAGE_FOLDER.'/tmp'),
    'Uploads folder is not writable: /storage/uploads'  => is_writable(COCKPIT_STORAGE_FOLDER.'/uploads'),
);

$failed = [];

foreach($checks as $info => $check) {

    if (!$check) {
        $failed[] = $info;
    }
}

if(count($failed)) {
    foreach ($failed as $info) {
        CLI::writeln($info, false);
    }
    exit(1);
}
else {
    CLI::writeln('No problems found', true);
    exit(0);
}
