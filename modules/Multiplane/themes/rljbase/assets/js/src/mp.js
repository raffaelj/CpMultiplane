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

// fix pure css mobile nav :target jump
MP.ready(function() {

    var nav         = document.getElementById('nav'),
        navButton   = nav ? nav.querySelector('a.icon-menu') : null,
        closeButton = nav ? nav.querySelector('a.icon-close') : null,
        anchorLinks = nav ? nav.querySelectorAll('ul a[href^="#"]') : null;

    if (nav && navButton && closeButton) {
        navButton.addEventListener('click', function(e) {
            if (e) e.preventDefault();
            nav.classList.add('mobile-nav-targeted');
        });
        closeButton.addEventListener('click', function(e) {
            if (e) e.preventDefault();
            nav.classList.remove('mobile-nav-targeted');
        });
    }

    // close mobile nav if navigating through page via anchor links
    if (anchorLinks && anchorLinks.length) {
        Array.prototype.forEach.call(anchorLinks, function(el) {
            el.addEventListener('click', function(e) {
                nav.classList.remove('mobile-nav-targeted');
            });
        });
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
