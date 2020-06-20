/**
 * npm run build:js
 * npm run watch:js
 */

var MP = require('./mp/MP.js');

MP.Cookie   = require('./mp/Cookie.js');
MP.Carousel = require('./mp/SimpleCarousel.js');
MP.Lightbox = require('./mp/SimpleLightbox.js');
MP.Video    = require('./mp/SimpleVideo.js');
MP.Privacy  = require('./mp/Privacy.js');
MP.Modal    = require('./mp/SimpleModalManager.js');
MP.Utils    = require('./mp/Utils.js');
MP.MailProtection = require('./mp/MailProtection.js');

// fire privacy event
// * to display cookie popup
// * to fire event after user accepted usage
MP.on('privacy', function(e) {

    if (e.params.event && e.params.cookie) {

        if (MP.Cookie.get(e.params.cookie) == '1') {
            MP.trigger(e.params.event, e.params);
        } else {
            MP.Privacy.displayPrivacyNotice(e.params);
        }

    }

});

// deprecated, but still necessary for video links in wysiwyg field
MP.convertVideoLinksToIframes = function() {
    MP.Video.convertVideoLinksToIframes();
};
MP.replaceVideoLink = function() {
    MP.Video.replaceVideoLink();
};

module.exports = MP;
