<?php
/**
 * bootstrap file of child theme demo
 * part of CpMultiplane
 * 
 * This file is loaded automatically, if it is in the root of a theme folder.
 */

// set assets
$this->set('multiplane.assets.top', [
    MP_BASE_URL.'/modules/Multiplane/themes/'.basename(__DIR__).'/assets/css/style.min.css', // main style file
]);
