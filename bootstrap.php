<?php
/**
 * CpMultiplane is a small PHP front end for Cockpit CMS
 * 
 * @see       https://github.com/raffaelj/CpMultiplane
 * @see       https://github.com/agentejo/cockpit/
 * 
 * @author    Raffael Jesche
 * @license   MIT
 * @note      work in progress, see package.json for version info
 */

// check for custom defines
if (file_exists(__DIR__.'/defines.php')) {
    include(__DIR__.'/defines.php');
}

// define some constants for later usage
if (!defined('MP_ADMINFOLDER'))     define('MP_ADMINFOLDER',  'cockpit');
if (!defined('MP_DIR'))             define('MP_DIR',          str_replace(DIRECTORY_SEPARATOR, '/', realpath(__DIR__)));

$MP_DOCS_ROOT = str_replace(DIRECTORY_SEPARATOR, '/', isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : dirname(__DIR__));
# make sure that $_SERVER['DOCUMENT_ROOT'] is set correctly
if (strpos(MP_DIR, $MP_DOCS_ROOT)!==0 && isset($_SERVER['SCRIPT_NAME'])) {
    $MP_DOCS_ROOT = str_replace(dirname(str_replace(DIRECTORY_SEPARATOR, '/', $_SERVER['SCRIPT_NAME'])), '', MP_DIR);
}
if (!defined('MP_DOCS_ROOT'))       define('MP_DOCS_ROOT',    $MP_DOCS_ROOT);

$BASE_URL = dirname(parse_url($_SERVER['SCRIPT_NAME'], PHP_URL_PATH));
if (!defined('MP_BASE_URL'))        define('MP_BASE_URL',      $BASE_URL === '/' ? '' : $BASE_URL);

if (!defined('MP_ENV_ROOT'))        define('MP_ENV_ROOT',     MP_DIR);
if (!defined('MP_ENV_URL'))         define('MP_ENV_URL',      MP_DIR == MP_ENV_ROOT ? MP_BASE_URL : MP_BASE_URL . str_replace(MP_DIR, '', MP_ENV_ROOT));

if (!defined('MP_CONFIG_DIR'))      define('MP_CONFIG_DIR',   MP_ENV_ROOT.'/config');

if (!defined('MP_CONFIG_PATH'))     define('MP_CONFIG_PATH',  MP_CONFIG_DIR.'/config.php');

// avoid overriding paths and don't bind routes - to do: cleaner implementation
if (!defined('MP_SELF_EXPORT'))     define('MP_SELF_EXPORT',  false);

// for thumbnails of CpMultiplane assets
if (!defined('COCKPIT_SITE_DIR'))   define('COCKPIT_SITE_DIR',  MP_ENV_ROOT);

if (!defined('COCKPIT_DIR'))        define('COCKPIT_DIR', MP_DIR.'/'.MP_ADMINFOLDER);

// include cockpit, now `$cockpit` and `cockpit()` are available
if (file_exists(COCKPIT_DIR . '/bootstrap.php')) {
    require_once(COCKPIT_DIR . '/bootstrap.php');
} else { echo '<!DOCTYPE html><html><body><p>You have to install <a href="https://github.com/agentejo/cockpit">Cockpit CMS</a> before you can use CpMultiplane.</p></body></html>'; die; } // to do: cockpit downloader

// load custom config
$customConfig = [];
if (\file_exists(MP_CONFIG_PATH)) {
    $customConfig = include(MP_CONFIG_PATH);
}

cockpit()->loadModules(array_merge([
    MP_DIR . '/modules', # core
    MP_ENV_ROOT . '/addons' # addons
], $customConfig['loadmodules'] ?? []));

// shorthand module call
function mp() {
    return cockpit('multiplane');
}
