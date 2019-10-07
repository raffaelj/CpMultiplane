
var d         = document,
    MP        = require('./mp/MP.js'),
    Cookie    = require('./mp/Cookie.js'),
    Video     = require('./mp/SimpleVideo.js'),
    Privacy   = require('./mp/Privacy.js')
    ;


MP.on('privacy', function(e) {

    // load video player for external videos
    if (e.params.type && e.params.type == 'external_video' && e.params.target) {

        if (Cookie.get('loadExternalVideos') == '1') {
            Video.loadVideo(e.params.target);
        } else {
            Privacy.displayPrivacyNotice(e.params);
        }

    }

});


// deprecated, but still necessary for video links in wysiwyg field
MP.convertVideoLinksToIframes = function() {
    Video.convertVideoLinksToIframes();
};
MP.replaceVideoLink = function() {
    Video.replaceVideoLink();
};

MP.Cookie   = Cookie;
MP.Carousel = require('./mp/SimpleCarousel.js');
MP.Lightbox = require('./mp/SimpleLightbox.js');
MP.Video    = Video;
MP.Privacy  = Privacy;

module.exports = MP;
