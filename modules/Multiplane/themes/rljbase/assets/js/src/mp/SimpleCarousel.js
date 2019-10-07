
var g  = window,
    d  = document
    MP = require('./MP.js');

module.exports = {

    selector:  '',
    carousels: [],
    autoplay:  true,
    duration:  15000,

    init: function(options) {

        var $this = this;

        // overwrite config
        if (options) {
            if (typeof options == 'string') {
                this.selector = options;
            } else {
                Object.keys(options).forEach(function(k) {
                    $this[k] = options[k];
                });
            }
        } else { return; }

        Array.prototype.forEach.call(d.querySelectorAll(this.selector), function (node, k) {

            $this.carousels.push({
                paused:   false,
                node:     node,
                interval: null,
            });

        });

        // stop, if no carousels found
        if (!this.carousels.length) return;

        Array.prototype.forEach.call(this.carousels, function (carousel, k) {

            var pauseButton = d.createElement('a');
            pauseButton.setAttribute('href', '#');
            pauseButton.classList.add('icon-pause');
            pauseButton.setAttribute('aria-label', 'start/stop image carousel');
            pauseButton.tabIndex = 0;

            carousel.node.appendChild(pauseButton);

            // resize with browser window - to do: find pure css solution
            g.addEventListener('resize', function() {
                carousel.node.style.height = carousel.node.children[0].offsetHeight + 'px';
            });

            // fire resize event, when page is loaded to get the right height,
            // otherwise there is a random chance for "0px" (image not loaded...)
            g.addEventListener('load', function() {
                var event;
                if (typeof Event === 'function') {
                    event = new Event('resize');
                } else {
                    event = d.createEvent('Event');
                    event.initEvent('resize', true, true);
                }
                g.dispatchEvent(event);
            });

            carousel.node.addEventListener('click', function(e) {

                if (e) e.preventDefault();

                carousel.paused = !carousel.paused;

                // to do: set cookie to pause sliders on other pages, too
                // Does this cookie need to be allowed via privacy settings (GDPR)?

                if (carousel.paused) {
                    pauseButton.classList.remove('icon-pause');
                    pauseButton.classList.add('icon-play');
                    $this.pause(carousel);
                } else {
                    pauseButton.classList.remove('icon-play');
                    pauseButton.classList.add('icon-pause');
                    $this.play(carousel);
                }

            });

            MP.visible(
                function(){$this.play(carousel);},
                function(){$this.pause(carousel);}
            );

            if ($this.autoplay && !carousel.paused) {
                $this.play(carousel);
            }

        });

    },

    play: function(carousel) {

        // don't replay on visibility change
        if (carousel.paused) return false;
        
        if (!carousel.interval) {
            carousel.interval = this.animate(carousel);
        }

        return carousel.interval;

    },

    pause: function(carousel) {

        window.clearInterval(carousel.interval);
        carousel.interval = false;

    },

    animate: function(carousel) {

        // start animation and return interval id
        var intervalId = setInterval(function() {

            var current = carousel.node.querySelector('.current');
            var next    = current.nextElementSibling;

            if (!next || next.nodeName == 'A') next = carousel.node.firstElementChild;

            // set src, if not done already, but don't load all images on startup
            if (next.getAttribute('src') == 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7') {
                next.setAttribute('src', next.dataset.src);
            }

            current.classList.remove('current');
            next.classList.add('current');

        }, this.duration);

        return intervalId;

    },

};
