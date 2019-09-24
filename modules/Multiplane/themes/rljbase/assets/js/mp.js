(function(g, d) {

    // for relative paths - MP_BASE_URL should be declared in the head of your
    // template file: <script>var MP_BASE_URL = '{{ MP_BASE_URL }}';</script>
    if (!MP_BASE_URL) MP_BASE_URL = '';

    var MP = {

        base_route : MP_BASE_URL,
        base_url   : MP_BASE_URL,

        // source: http://youmightnotneedjquery.com/#ready
        ready: function (fn) {
            if (d.readyState != 'loading'){
                fn();
            } else if (d.addEventListener) {
                d.addEventListener('DOMContentLoaded', fn);
            } else {
                d.attachEvent('onreadystatechange', function() {
                    if (d.readyState != 'loading')
                        fn();
                });
            }
        },

        visible: function(fn, fm) {

            // inspired by: https://stackoverflow.com/a/19519701
            var stateKey, eventKey, keys = {
                hidden:       'visibilitychange',
                webkitHidden: 'webkitvisibilitychange',
                mozHidden:    'mozvisibilitychange',
                msHidden:     'msvisibilitychange'
            };

            for (stateKey in keys) {
                if (stateKey in d) {
                    eventKey = keys[stateKey];
                    break;
                }
            }

            if (typeof fn == 'function' && typeof fm == 'function') {
                d.addEventListener(eventKey, function() {
                    if (!d[stateKey]) fn(); // visible
                    else              fm(); // invisible
                });
            }
            else if (typeof fn == 'function') {
                d.addEventListener(eventKey, function() {
                    fn();
                });
            }

            return !d[stateKey];

        },

        // deprecated, but still necessary for video links in wysiwyg field
        convertVideoLinksToIframes: function() {

            var $this = this;

            var video_links = d.querySelectorAll('a[data-video-id]');

            Array.prototype.forEach.call(video_links, function(el, i){

                var id       = el.getAttribute('data-video-id');
                var provider = el.getAttribute('data-video-provider');
                var asset    = el.getAttribute('data-video-thumb');
                var width    = 480;
                var height   = 370;
                var ratio    = '';

                if ((data_width = el.getAttribute('data-video-width'))
                    && (data_height = el.getAttribute('data-video-height'))) {

                    // reassign aspect ratio
                    height = width * (data_height / data_width);
                    ratio  = (data_width / data_height == 16 / 9) ? '16-9' : '4-3';

                }

                var thumb = MP_BASE_URL + '/getImage?src=' + asset + '&w=480&o=1';

                if (provider == 'youtube') {

                    // downloaded thumbnails are always 4:3 (640px x 480px) with black borders
                    // lazy fix: overwrite ratio
                    ratio = '16-9';

                    var src = 'https://www.youtube-nocookie.com/embed/'
                        + id + '?enablejsapi=1&rel=0&showinfo=0&autoplay=1';

                }

                if (provider == 'vimeo') {
                    var src = 'https://player.vimeo.com/video/'
                        + id + '?color=ffffff&title=0&byline=0&portrait=0&autoplay=1';
                }

                var container = d.createElement('div');

                container.classList.add('video_embed_container');
                container.classList.add('ratio-' + ratio);

                var iframe = d.createElement('iframe');

                iframe.classList.add('video_embed');
                iframe.setAttribute('width', width);
                iframe.setAttribute('height', height);
                iframe.setAttribute('src', 'about:blank');
                iframe.setAttribute('data-src', src);
                iframe.setAttribute('data-provider', provider);
                iframe.setAttribute('src', 'about:blank');
                iframe.setAttribute('allow', 'autoplay; fullscreen');
                iframe.style['background-image'] = 'url(' + thumb + ')';

                if (!iframe.getAttribute('id')) {
                    iframe.setAttribute('id', 'player_' + Math.random().toString(36).substring(2));
                }

                iframe.tabIndex = -1;

                container.appendChild(iframe);

                var play_button = d.createElement('a');
                play_button.setAttribute('class', 'icon-play');
                play_button.setAttribute('href', '#');
                play_button.tabIndex = 0;

                container.appendChild(play_button);

                el.parentNode.insertBefore(container, el);
                el.parentNode.style['text-align'] = 'center';

                play_button.addEventListener('click', function(e) {

                    if (e) e.preventDefault();  

                    if (Cookie.get('loadExternalVideos') == '1') {
                        $this.loadVideo(iframe);
                    }
                    else {
                        $this.displayPrivacyNotice(iframe);
                    }

                });

            });

        },

        replaceVideoLink: function() {

            // to do: fix disabled autoplay on mobile device
            // seems to not work without youtube iframe player api
            // https://developers.google.com/youtube/iframe_api_reference
            // or with a fake click event...

            var $this = this;

            var video_links = d.querySelectorAll('.video_embed');

            Array.prototype.forEach.call(video_links, function(iframe, i) {

                iframe.tabIndex = -1;

                if (!iframe.getAttribute('id')) {
                    iframe.setAttribute('id', 'player_' + Math.random().toString(36).substring(2));
                }

                var play_button = iframe.nextElementSibling;

                play_button.addEventListener('click', function(e) {

                    if (e) e.preventDefault();  

                    if (Cookie.get('loadExternalVideos') == '1') {
                        $this.loadVideo(iframe);
                    }
                    else {
                        $this.displayPrivacyNotice(iframe);
                    }

                });

            });

        },

        displayPrivacyNotice: function (target) {

            var $this = this;

            var banner = d.getElementById('privacy-notice');
            banner.style.display = 'block';

            var lastFocus = target;
            banner.tabIndex = -1;
            banner.setAttribute('role', 'dialog');
            banner.focus();

            var form = d.getElementById('privacy-notice-form');

            form.addEventListener('submit', function(e) {

                if (e) e.preventDefault();

                var data = new FormData(form);

                var loadExternalVideos = data.get('loadExternalVideos');

                // Cookie won't be set, if loadExternalVideos == null
                Cookie.set('loadExternalVideos', loadExternalVideos);

                if (loadExternalVideos && target && target.nodeName == 'IFRAME') {
                    $this.loadVideo(target);
                }

                // hide banner
                banner.style.display = '';

                lastFocus.focus();

            });

            form.addEventListener('reset', function(e) {

                banner.style.display = '';

                lastFocus.focus();

            });

        },

        route: function(url) {
            return this.base_route+url;
        },
        
        // source: https://github.com/agentejo/cockpit/blob/next/assets/app/js/app.js#L25
        // Cockpit CMS, Artur Heinze, MIT License
        request: function(url, data, type) {

            url  = this.route(url);
            type = type || 'json';

            return new Promise(function (fulfill, reject){

                var xhr = new XMLHttpRequest();

                xhr.open('post', url, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                url += (url.indexOf('?') !== -1 ? '&':'?') + 'nc=' + Math.random().toString(36).substr(2);

                if (data) {

                    if (typeof(data) === 'object' && data instanceof HTMLFormElement) {
                        data = new FormData(data);
                    } else if (typeof(data) === 'object' && data instanceof FormData) {
                        // do nothing
                    } else if (typeof(data) === 'object') {

                        xhr.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');
                        data = JSON.stringify(data || {});
                    }
                }

                xhr.onloadend = function () {

                    var resdata = xhr.responseText;

                    if (type == 'json') {
                        try {
                            resdata = JSON.parse(xhr.responseText);
                        } catch(e) {
                            resdata = null;
                        }
                    }

                    if (this.status == 200) {
                        fulfill(resdata, xhr);
                    } else {
                        reject(resdata, xhr);
                    }
                };

                // send the collected data as JSON
                xhr.send(data);
            });
        },

        loadVideo: function(iframe) {

            var $this = this,
                id = iframe.getAttribute('id')
                volume = iframe.dataset.volume || 50;

            // accessibility
            iframe.focus();
            iframe.removeAttribute('tabindex');

            // switch data src
            iframe.setAttribute('src', iframe.getAttribute('data-src'));
            iframe.style['background-image'] = '';

            if (iframe.dataset.provider == 'youtube') {

                this.callPlayer(id, function() {
                    $this.callPlayer(id, 'setVolume', [volume, true]);
                    $this.callPlayer(id, 'playVideo');
                });

            }

        },

        /**
         * @author       Rob W <gwnRob@gmail.com>
         * @website      https://stackoverflow.com/a/7513356/938089
         * @version      20190409
         * @description  Executes function on a framed YouTube video (see website link)
         *               For a full list of possible functions, see:
         *               https://developers.google.com/youtube/js_api_reference
         * @param String frame_id The id of (the div containing) the frame
         * @param String func     Desired function to call, eg. "playVideo"
         *        (Function)      Function to call when the player is ready.
         * @param Array  args     (optional) List of arguments to pass to function func*/
        callPlayer: function (frame_id, func, args) {

            // slightly modified version to fit into the MP object
            // to do: cleanup

            var $this = this;

            if (window.jQuery && frame_id instanceof jQuery) frame_id = frame_id.get(0).id;
            var iframe = document.getElementById(frame_id);
            if (iframe && iframe.tagName.toUpperCase() != 'IFRAME') {
                iframe = iframe.getElementsByTagName('iframe')[0];
            }

            // When the player is not ready yet, add the event to a queue
            // Each frame_id is associated with an own queue.
            // Each queue has three possible states:
            //  undefined = uninitialised / array = queue / .ready=true = ready
            if (!this.callPlayer.queue) this.callPlayer.queue = {};
            var queue = this.callPlayer.queue[frame_id],
                domReady = document.readyState == 'complete';

            if (domReady && !iframe) {
                // DOM is ready and iframe does not exist. Log a message
                window.console && console.log('callPlayer: Frame not found; id=' + frame_id);
                if (queue) clearInterval(queue.poller);
            } else if (func === 'listening') {
                // Sending the "listener" message to the frame, to request status updates
                if (iframe && iframe.contentWindow) {
                    func = '{"event":"listening","id":' + JSON.stringify(''+frame_id) + '}';
                    iframe.contentWindow.postMessage(func, '*');
                }
            } else if ((!queue || !queue.ready) && (
                       !domReady ||
                       iframe && !iframe.contentWindow ||
                       typeof func === 'function')) {
                if (!queue) queue = this.callPlayer.queue[frame_id] = [];
                queue.push([func, args]);
                if (!('poller' in queue)) {
                    // keep polling until the document and frame is ready
                    queue.poller = setInterval(function() {
                        $this.callPlayer(frame_id, 'listening');
                    }, 250);
                    // Add a global "message" event listener, to catch status updates:
                    messageEvent(1, function runOnceReady(e) {
                        if (!iframe) {
                            iframe = document.getElementById(frame_id);
                            if (!iframe) return;
                            if (iframe.tagName.toUpperCase() != 'IFRAME') {
                                iframe = iframe.getElementsByTagName('iframe')[0];
                                if (!iframe) return;
                            }
                        }
                        if (e.source === iframe.contentWindow) {
                            // Assume that the player is ready if we receive a
                            // message from the iframe
                            clearInterval(queue.poller);
                            queue.ready = true;
                            messageEvent(0, runOnceReady);
                            // .. and release the queue:
                            while (tmp = queue.shift()) {
                                $this.callPlayer(frame_id, tmp[0], tmp[1]);
                            }
                        }
                    }, false);
                }
            } else if (iframe && iframe.contentWindow) {
                // When a function is supplied, just call it (like "onYouTubePlayerReady")
                if (func.call) return func();
                // Frame exists, send message
                iframe.contentWindow.postMessage(JSON.stringify({
                    "event": "command",
                    "func": func,
                    "args": args || [],
                    "id": frame_id
                }), "*");
            }
            /* IE8 does not support addEventListener... */
            function messageEvent(add, listener) {
                var w3 = add ? window.addEventListener : window.removeEventListener;
                w3 ?
                    w3('message', listener, !1)
                :
                    (add ? window.attachEvent : window.detachEvent)('onmessage', listener);
            }
        },

    };

    var Cookie = {

        lifeTime: '30', // cookie life time in days

        set: function(key, value, lifeTime) {

            if (!key || (!value && value != 0)) return;
            if (!lifeTime && lifeTime != 0) lifeTime = this.lifeTime;

            var expirationDate = new Date();
            expirationDate.setTime(expirationDate.getTime() + lifeTime * 86400000)

            d.cookie = key + '=' + value + ';expires=' + expirationDate.toUTCString() + '; path=/';

        },

        get: function(key) {

            if (d.cookie == '') return;

            // source: https://stackoverflow.com/a/42578414
            var cockie = d.cookie.split('; ').reduce(function(result, pairStr) {
                var arr = pairStr.split('=');
                if (arr.length === 2) { result[arr[0]] = arr[1]; }
                return result;
            }, {});

            return key ? cockie[key] : cockie;

        },

        destroy: function(key) {

            this.set(key, '', 0);

        }

    };

    var SimpleLightbox = {

        selector:       '',
        group:          null,
        active:         false,
        currentItem:    null,
        currentGallery: null,
        galleries:      [],
        captions:       [],
        img:            d.createElement('img'),
        lightbox:       d.createElement('div'),
        wrap:           d.createElement('div'),
        caption:        d.createElement('div'),
        closeButton:    d.createElement('a'),
        prevButton:     d.createElement('a'),
        nextButton:     d.createElement('a'),
        lastFocus:      null,

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

            this.lightbox.setAttribute('class', 'lightbox');
            this.lightbox.setAttribute('role', 'dialog');
            this.lightbox.setAttribute('aria-hidden', 'true');
            this.lightbox.tabIndex = -1;

            d.querySelector('body').appendChild(this.lightbox);

            this.prevButton.setAttribute('href', '#');
            this.nextButton.setAttribute('href', '#');
            this.closeButton.setAttribute('href', '#');
            
            this.prevButton.setAttribute('aria-label', 'previous');
            this.nextButton.setAttribute('aria-label', 'next');
            this.closeButton.setAttribute('aria-label', 'close');

            this.prevButton.classList.add('prev');
            this.nextButton.classList.add('next');
            this.closeButton.classList.add('icon-close');

            this.lightbox.appendChild(this.wrap);
            this.lightbox.appendChild(this.prevButton);
            this.lightbox.appendChild(this.nextButton);
            this.lightbox.appendChild(this.closeButton);
            this.wrap.appendChild(this.img);

            if (this.group) {
                var groups = d.querySelectorAll(this.group);
                Array.prototype.forEach.call(groups, function(el, i) {
                    $this.galleries.push(el.querySelectorAll($this.selector));
                });
            }
            else {
                this.galleries.push(d.querySelectorAll(this.selector));
            }

            Array.prototype.forEach.call(this.galleries, function(gallery, k) {

                $this.captions[k] = {};

                Array.prototype.forEach.call(gallery, function(el, i) {

                    el.addEventListener('click', function(e) {

                        if (e) e.preventDefault();
                        
                        $this.lastFocus = el || d.activeElement;

                        $this.active         = true;
                        $this.currentItem    = i;
                        $this.currentGallery = k;

                        $this.update();

                    });

                    // find caption
                    var node = (el.parentNode).querySelector('figcaption');
                    if (node) {
                        $this.captions[k][i] = node.innerHTML;
                    } else if (el.getAttribute('title')) {
                        $this.captions[k][i] = el.getAttribute('title');
                    } else if (el.dataset.title) {
                        $this.captions[k][i] = el.dataset.title;
                    }

                });
            });

            this.prevButton.addEventListener('click', function(e) {
                if (e) {e.preventDefault();e.stopPropagation();};
                $this.prev(e);
            });

            this.nextButton.addEventListener('click', function(e) {
                if (e) {e.preventDefault();e.stopPropagation();};
                $this.next(e);
            });

            this.closeButton.addEventListener('click', function(e) {
                if (e) e.preventDefault();
            });

            // close lightbox on click
            this.lightbox.addEventListener('click', function(e) {
                if (e && e.target.nodeName == 'IMG') {
                    return; // don't close when clicking on image
                }
                $this.close(e);
            });

            // force focus to modal
            d.addEventListener('focus', function(e) {
                if ($this.active && !$this.lightbox.contains(e.target)) {
                    e.stopPropagation();
                    $this.lightbox.focus();
                }
            }, true);

            d.addEventListener('keydown', function(e) {
                if ($this.active) {
                    if (e.keyCode == 37) $this.prev(e);
                    if (e.keyCode == 39) $this.next(e);
                    if (e.keyCode == 27) $this.close(e);
                }
            });

        },

        update: function() {

            // show/hide lightbox
            if (!this.active) {
                this.lightbox.classList.remove('active');

                // accessibility
                this.lightbox.setAttribute('aria-hidden', 'true');
                this.lastFocus.focus();
                return;
            }

            this.lightbox.classList.add('active');

            // accessibility
            this.lightbox.focus();

            // hide first/last prev/next buttons
            if (this.currentItem == 0) {
                this.prevButton.classList.add('hidden');
            } else this.prevButton.classList.remove('hidden');

            if (this.currentItem == this.galleries[this.currentGallery].length -1) {
                this.nextButton.classList.add('hidden');
            }
            else this.nextButton.classList.remove('hidden');

            // switch image
            this.img.setAttribute('src', this.galleries[this.currentGallery][this.currentItem].getAttribute('href'));

            // switch caption
            if (this.captions[this.currentGallery][this.currentItem]) {
                this.caption.innerHTML = this.captions[this.currentGallery][this.currentItem];
                this.wrap.appendChild(this.caption);
            } else if (this.wrap.contains(this.caption)) {
                this.wrap.removeChild(this.caption);
            }

        },

        prev: function(e) {
            if (this.galleries[this.currentGallery][this.currentItem - 1]) {
                this.currentItem -= 1;
                this.update();
            }
        },

        next: function(e) {
            if (this.galleries[this.currentGallery][this.currentItem + 1]) {
                this.currentItem += 1;
                this.update();
            }
        },

        close: function(e) {
            this.active = false;
            this.update();
        },

    };

    var SimpleCarousel = {

        selector: '',
        carousels: [],
        autoplay: true,
        duration: 15000,

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

    MP.Cookie   = Cookie;
    MP.Lightbox = SimpleLightbox;
    MP.Carousel = SimpleCarousel;

    g.MP = MP;

})(this, document);
