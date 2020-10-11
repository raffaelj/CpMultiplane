<?php

// set default timezone
date_default_timezone_set('UTC');

// handle php webserver
if (PHP_SAPI == 'cli-server' && is_file(__DIR__.parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

// bootstrap CpMultiplane
require(__DIR__.'/bootstrap.php');

# admin route
if (!defined('MP_ROUTE')) {
    $route = preg_replace('#'.preg_quote(MP_BASE_URL, '#').'#', '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), 1);
    define('MP_ROUTE', $route == '' ? '/' : $route);
}

// run app
$cockpit->set('route', MP_ROUTE)->trigger('multiplane.init')->run();
