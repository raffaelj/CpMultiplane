<?php
/**
 * CpMultiplane is a small PHP front end for Cockpit CMS
 * 
 * @see       https://github.com/raffaelj/CpMultiplane
 * @see       https://github.com/agentejo/cockpit/
 * 
 * @version   0.1.1
 * @author    Raffael Jesche
 * @license   MIT
 * @note      work in progress
 */
$version = '0.1.1';

// check for custom defines
if (file_exists(__DIR__.'/defines.php')) {
    include(__DIR__.'/defines.php');
}

// define some constants for later usage
if (!defined('MP_ADMINFOLDER'))   define('MP_ADMINFOLDER',  'cockpit');
if (!defined('MP_DOCS_ROOT'))     define('MP_DOCS_ROOT',    str_replace(DIRECTORY_SEPARATOR, '/', realpath(__DIR__)));
if (!defined('MP_BASE_ROOT'))     define('MP_BASE_ROOT',    basename(MP_DOCS_ROOT));

$BASE_URL = dirname(parse_url($_SERVER['SCRIPT_NAME'], PHP_URL_PATH));
if (!defined('MP_BASE_URL'))      define('MP_BASE_URL',      $BASE_URL === '/' ? '' : $BASE_URL);

if (!defined('MP_CONFIG_DIR'))    define('MP_CONFIG_DIR',   MP_DOCS_ROOT.'/config');
if (!defined('MP_CONFIG_PATH'))   define('MP_CONFIG_PATH',  MP_CONFIG_DIR.'/config.php');

if (!defined('COCKPIT_CLI'))      define('COCKPIT_CLI', PHP_SAPI == 'cli');

// for thumbnails of CpMultiplane assets
if (!defined('COCKPIT_SITE_DIR'))   define('COCKPIT_SITE_DIR',  MP_DOCS_ROOT);

// include cockpit, now `$cockpit` and `cockpit()` are available
if (file_exists(MP_DOCS_ROOT . '/' . MP_ADMINFOLDER . '/bootstrap.php')) {
    require_once(MP_DOCS_ROOT . '/' . MP_ADMINFOLDER . '/bootstrap.php');
} else { echo "Cockpit doesn't exist."; die; } // to do: cockpit downloader

//set version
$cockpit->set('multiplane/version', $version);

// load custom config
if (file_exists(MP_CONFIG_PATH)) {
    $config = include(MP_CONFIG_PATH);
}

$cockpit->loadModules(array_merge([
    MP_DOCS_ROOT.'/modules',  # core
    MP_DOCS_ROOT.'/addons' # addons
], $config['loadmodules'] ?? []));
