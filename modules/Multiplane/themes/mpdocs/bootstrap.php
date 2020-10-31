<?php

// set assets
$this->set('multiplane.assets.top', [
    'theme:assets/css/style.min.css', // main style file
]);

$this->set('multiplane.assets.bottom', [
    'theme:assets/js/mp.min.js',
    'theme:assets/lib/highlight/highlight.pack.js',
]);

// convertVideoLinksToIframes() is depricated --> fallback for video links in wysiwyg field
mp()->add('scripts', [
'MP.ready(function() {
    MP.MailProtection.init({selector:"main"});
    MP.replaceVideoLink();
    MP.convertVideoLinksToIframes();
    MP.Lightbox.init({group:".gallery",selector:"a"});

    hljs.initHighlightingOnLoad();
});'
]);
//     MP.Carousel.init({selector:".carousel"});
