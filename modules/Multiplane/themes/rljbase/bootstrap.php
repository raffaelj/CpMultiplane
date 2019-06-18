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
    MP_BASE_URL.'/modules/Multiplane/themes/rljbase/assets/lib/wa-mediabox/wa-mediabox.min.css' // gallery lightbox
]);
$this->set('multiplane.assets.bottom', [
    MP_BASE_URL.'/modules/Multiplane/themes/rljbase/assets/lib/wa-mediabox/wa-mediabox.min.js', // gallery lightbox
    MP_BASE_URL.'/modules/Multiplane/themes/rljbase/assets/js/mp.js',          // Multiplane js
]);

// $this->module('multiplane')->hasBackgroundImage = true;

// extend lexy parser for custom image resizing
$this->renderer->extend(function($content){ // returns relative url of scaled logo
    return preg_replace('/(\s*)@logo\((.+?)\)/', '$1<?php echo MP_BASE_URL."/getImage?src=".urlencode($2)."&w=".$app->retrieve("multiplane/lexy/logo/width", 200)."&h=".$app->retrieve("multiplane/lexy/logo/height", 200)."&q=".$app->retrieve("multiplane/lexy/logo/quality", 80); ?>', $content);
});

$this->renderer->extend(function($content) { // returns relative url of image
    return preg_replace('/(\s*)@uploads\((.+?)\)/', '$1<?php echo MP_BASE_URL; $app->base("#uploads:" . $2); ?>', $content);
});

$this->renderer->extend(function($content){ // returns relative url of scaled image (thumbnail)
    return preg_replace('/(\s*)@thumbnail\((.+?)\)/', '$1<?php echo MP_BASE_URL."/getImage?src=".urlencode($2)."&w=".$app->retrieve("multiplane/lexy/thumbnail/width", 100)."&h=".$app->retrieve("multiplane/lexy/thumbnail/height", 100)."&q=".$app->retrieve("multiplane/lexy/thumbnail/quality", 70)."&m=".$app->retrieve("multiplane/lexy/thumbnail/mode", "thumbnail"); ?>', $content);
});

$this->renderer->extend(function($content){ // returns relative url of scaled image (image)
    return preg_replace('/(\s*)@image\((.+?)\)/', '$1<?php echo MP_BASE_URL."/getImage?src=".urlencode($2)."&w=".$app->retrieve("multiplane/lexy/image/width", 800)."&h=".$app->retrieve("multiplane/lexy/image/height", 800)."&q=".$app->retrieve("multiplane/lexy/image/quality", 80)."&m=".$app->retrieve("multiplane/lexy/image/mode", "bestFit"); ?>', $content);
});

$this->renderer->extend(function($content){ // returns relative url of scaled image (headerimage)
    return preg_replace('/(\s*)@headerimage\((.+?)\)/', '$1<?php echo MP_BASE_URL."/getImage?src=".urlencode($2)."&w=".$app->retrieve("multiplane/lexy/headerimage/width", 800)."&h=".$app->retrieve("multiplane/lexy/headerimage/height", 200)."&q=".$app->retrieve("multiplane/lexy/headerimage/quality", 80)."&m=".$app->retrieve("multiplane/lexy/headerimage/mode", "thumbnail"); ?>', $content);
});
