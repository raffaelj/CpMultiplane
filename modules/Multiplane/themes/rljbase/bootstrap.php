<?php
/**
 * bootstrap file of rljBase theme
 * part of CpMultiplane
 * 
 * This file is loaded automatically, if it is in the root of a theme folder.
 */

// pass custom layout file to LimeExtra
$this->layout = 'views:base.php';

// add assets
$this->set('multiplane.assets.top', [
    MP_BASE_URL.'/modules/Multiplane/themes/rljbase/assets/css/style.min.css', // main style file
]);

$this->on('multiplane.findone.after', function(&$page) {

    $collection = $this->module('collections')->collection(mp()->collection);

    $loadMpJs = false;
    $mpJsInit = [];

    if (isset($collection['fields']) && is_array($collection['fields'])) {

        foreach($collection['fields'] as $field) {

            if ($field['type'] == 'videolink') {
                $mpJsInit[] = 'MP.replaceVideoLink();';
            }

            if ($field['type'] == 'gallery' || $field['type'] == 'simple-gallery') {

                if ($field['name'] == 'carousel' || $field['name'] == 'slider') {
                    $mpJsInit[] = 'MP.Carousel.init({selector:".carousel"});';
                }
                else {
                    $mpJsInit[] = 'MP.Lightbox.init({group:".gallery",selector:"a"});';
                }

            }

        }

    }

    if (!empty($mpJsInit)) {

        // Multiplane js
        $this->set('multiplane.assets.bottom', [
            MP_BASE_URL.'/modules/Multiplane/themes/rljbase/assets/js/mp.js',
        ]);

        mp()->add('scripts', [
            "MP.ready(function() {\r\n". implode("\r\n", array_unique($mpJsInit)) ."\r\n});",
        ]);

    }

}, 100);
