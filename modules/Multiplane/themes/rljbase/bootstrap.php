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

$this->on('multiplane.page', function(&$page, &$posts, &$site) {

    $collection = $this->module('collections')->collection(mp()->collection);

    $loadMpJs = false;
    $mpJsInit = [];

    if (isset($collection['fields']) && is_array($collection['fields'])) {

        foreach($collection['fields'] as $field) {

            if (isset($this['modules']['videolinkfield']) && $field['type'] == 'videolink') {
                $mpJsInit[] = 'MP.replaceVideoLink();';
            }

            if (isset($this['modules']['videolinkfield']) && $field['type'] == 'wysiwyg') {
                // depricated, fallback for video links in wysiwyg field
                $mpJsInit[] = 'MP.convertVideoLinksToIframes();';
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
    
    if ($posts && isset($this['modules']['videolinkfield'])) {

        $collection = $this->module('collections')->collection(mp()->posts);

        if (isset($collection['fields']) && is_array($collection['fields'])) {
            foreach($collection['fields'] as $field) {
                if ($field['type'] == 'wysiwyg'
                    && ($field['name'] == 'content' || $field['name'] == 'excerpt')
                    ) {
                    // depricated, fallback for video links in wysiwyg field
                    $mpJsInit[] = 'MP.convertVideoLinksToIframes();';
                    break;
                }
            }
        }
    }

    if (!empty($mpJsInit)) {

        // Multiplane js
        $this->set('multiplane.assets.bottom', [
            MP_BASE_URL.'/modules/Multiplane/themes/rljbase/assets/js/mp.min.js',
        ]);

        mp()->add('scripts', [

            // IE polyfills
            'if(/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write(\'<script src="'.MP_BASE_URL.'/modules/Multiplane/themes/rljbase/assets/polyfills/FormData/formdata.min.js'.'"><\/script>\');' . "\r\n",

            // init functions, when document is ready
            "MP.ready(function() {\r\n". implode("\r\n", array_unique($mpJsInit)) ."\r\n});",
        ]);

    }

}, 100);
