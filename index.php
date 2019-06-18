<?php

// set default timezone
date_default_timezone_set('UTC');

// handle php webserver
if (PHP_SAPI == 'cli-server' && is_file(__DIR__.parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

// bootstrap CpMultiplane
require(__DIR__.'/bootstrap.php');

// run app
$cockpit->trigger('multiplane.init')->run();
