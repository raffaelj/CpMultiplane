<?php
/**
 * bootstrap file of child theme demo
 * part of CpMultiplane
 * 
 * This file is loaded automatically, if it is in the root of a theme folder.
 */

// add assets
$this->set('multiplane.assets.top', [
    MP_BASE_URL.'/modules/Multiplane/themes/'.basename(__DIR__).'/assets/css/style.min.css', // main style file
]);

$this->set('multiplane.assets.bottom', [
    MP_BASE_URL.'/modules/Multiplane/themes/rljbase/assets/js/mp.min.js',
]);

mp()->add('scripts', [
'MP.ready(function() {
    MP.replaceVideoLink();
    MP.convertVideoLinksToIframes();
    MP.Lightbox.init({group:".gallery",selector:"a"});
    MP.Carousel.init({selector:".carousel"});
});'
]);