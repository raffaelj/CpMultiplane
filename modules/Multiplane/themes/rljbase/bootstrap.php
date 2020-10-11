<?php
/**
 * bootstrap file of rljBase theme
 * part of CpMultiplane
 * 
 * This file is loaded automatically, if it is in the root of a theme folder.
 */

// pass custom layout file to LimeExtra
$this->layout = 'views:index.php';

// set assets
$this->set('multiplane.assets.top', [
    'theme:assets/css/style.min.css', // main style file
]);

$this->set('multiplane.assets.bottom', [
    'theme:assets/js/mp.min.js',
]);

// convertVideoLinksToIframes() is depricated --> fallback for video links in wysiwyg field
mp()->add('scripts', [
'MP.ready(function() {
    MP.replaceVideoLink();
    MP.convertVideoLinksToIframes();
    MP.Lightbox.init({group:".gallery",selector:"a"});
    MP.Carousel.init({selector:".carousel"});
});'
]);
