<?php
/**
 * bootstrap file of rljBase theme
 * part of CpMultiplane
 * 
 * This file is loaded automaticalle, if it is in the root of a theme folder.
 */

// pass custom layout file to LimeExtra
$this->layout = 'views:base.php';

// add assets
$this->set('multiplane.assets.top', [
    MP_BASE_URL.'/modules/Multiplane/themes/rljbase/assets/css/style.min.css', // main style file
]);
$this->set('multiplane.assets.bottom', [
    MP_BASE_URL.'/modules/Multiplane/themes/rljbase/assets/js/mp.js',          // Multiplane js
]);
$this->module('multiplane')->add('scripts', ['MP.Lightbox.init({group:".gallery",selector:"a"})']);

// $this->module('multiplane')->hasBackgroundImage = true;

// extend lexy parser for custom image resizing
$this->renderer->extend(function($content){ // returns relative url of scaled logo
    return preg_replace('/(\s*)@logo\((.+?)\)/', '$1<?php echo MP_BASE_URL."/getImage?src=".urlencode($2)."&w=".mp()->get("lexy/logo/width", 200)."&h=".mp()->get("lexy/logo/height", 200)."&q=".mp()->get("lexy/logo/quality", 80); ?>', $content);
});

$this->renderer->extend(function($content) { // returns relative url of image
    return preg_replace('/(\s*)@uploads\((.+?)\)/', '$1<?php echo MP_BASE_URL; $app->base("#uploads:" . $2); ?>', $content);
});

$this->renderer->extend(function($content){ // returns relative url of scaled image (thumbnail)
    return preg_replace('/(\s*)@thumbnail\((.+?)\)/', '$1<?php echo MP_BASE_URL."/getImage?src=".urlencode($2)."&w=".mp()->get("lexy/thumbnail/width", 100)."&h=".mp()->get("lexy/thumbnail/height", 100)."&q=".mp()->get("lexy/thumbnail/quality", 70)."&m=".mp()->get("lexy/thumbnail/method", "thumbnail"); ?>', $content);
});

$this->renderer->extend(function($content){ // returns relative url of scaled image (image)
    return preg_replace('/(\s*)@image\((.+?)\)/', '$1<?php echo MP_BASE_URL."/getImage?src=".urlencode($2)."&w=".mp()->get("lexy/image/width", 800)."&h=".mp()->get("lexy/image/height", 800)."&q=".mp()->get("lexy/image/quality", 80)."&m=".mp()->get("lexy/image/method", "bestFit"); ?>', $content);
});

$this->renderer->extend(function($content){ // returns relative url of scaled image (headerimage)
    return preg_replace('/(\s*)@headerimage\((.+?)\)/', '$1<?php echo MP_BASE_URL."/getImage?src=".urlencode($2)."&w=".mp()->get("lexy/headerimage/width",800)."&h=".mp()->get("lexy/headerimage/height", 200)."&q=".mp()->get("lexy/headerimage/quality", 80)."&m=".mp()->get("lexy/headerimage/method", "thumbnail"); ?>', $content);
});
