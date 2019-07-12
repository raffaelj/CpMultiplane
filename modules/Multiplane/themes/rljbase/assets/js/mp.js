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

        convertVideoLinksToIframes: function() {

            var $this = this;

            var video_links = d.querySelectorAll('a[data-video-id]');

            Array.prototype.forEach.call(video_links, function(el, i){

                var id       = el.getAttribute('data-video-id');
                var provider = el.getAttribute('data-video-provider');
                var asset    = el.getAttribute('data-video-thumb');
                var width    = 480;
                var height   = 370;

                if ((data_width = el.getAttribute('data-video-width'))
                    && (data_height = el.getAttribute('data-video-height'))) {

                    // reassign aspect ratio
                    height = width * (data_height / data_width);

                }

                var thumb = MP_BASE_URL + '/getImage?src=' + asset + '&w=480&o=1';

                if (provider == 'youtube') {
                    var src = 'https://www.youtube-nocookie.com/embed/'
                        + id + '?rel=0&showinfo=0&autoplay=1';
                }

                if (provider == 'vimeo') {
                    var src = 'https://player.vimeo.com/video/'
                        + id + '?color=ffffff&title=0&byline=0&portrait=0&autoplay=1';
                }

                var container = d.createElement('div');

                container.setAttribute('class', 'video_embed_container');

                var iframe = d.createElement('iframe');

                iframe.setAttribute('class', 'video_embed');
                iframe.setAttribute('width', width);
                iframe.setAttribute('height', height);
                iframe.setAttribute('src', 'about:blank');
                iframe.setAttribute('data-src', src);
                iframe.setAttribute('src', 'about:blank');
                iframe.setAttribute('allowfullscreen', '');
                iframe.style.width = width+'px';
                iframe.style.height = height+'px';
                iframe.style['background-image'] = 'url(' + thumb + ')';

                container.appendChild(iframe);

                var play_button = d.createElement('span');
                play_button.setAttribute('class', 'play_button');

                container.appendChild(play_button);

                el.parentNode.insertBefore(container, el);
                el.parentNode.style['text-align'] = 'center';

                play_button.addEventListener('click', function(e) {

                    if (Cookie.get('loadExternalVideos') == '1') {
                        iframe.setAttribute('src', iframe.getAttribute('data-src'));
                        iframe.style['background-image'] = '';
                    }
                    else {
                        $this.displayPrivacyNotice(iframe);
                    }

                });

            });

        },

        displayPrivacyNotice: function (target) {

            var banner = d.getElementById('privacy-notice');
            banner.style.display = 'block';

            var form = d.getElementById('privacy-notice-form');

            form.addEventListener('submit', function(e) {

                if (e) e.preventDefault();

                var data = new FormData(form);

                var loadExternalVideos = data.get('loadExternalVideos');

                // Cookie won't be set, if loadExternalVideos == null
                Cookie.set('loadExternalVideos', loadExternalVideos);

                if (loadExternalVideos) {
                    target.setAttribute('src', target.getAttribute('data-src'));
                    target.style['background-image'] = '';
                }

                // hide banner
                banner.style.display = '';

            });

            form.addEventListener('reset', function(e) {

                banner.style.display = '';

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
        body:           d.querySelector('body'),
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
            }

            this.lightbox.setAttribute('class', 'lightbox');
            d.querySelector('body').appendChild(this.lightbox);

            this.prevButton.classList.add('prev');
            this.nextButton.classList.add('next');
            this.closeButton.classList.add('close');

            this.lightbox.appendChild(this.wrap);
            this.lightbox.appendChild(this.prevButton);
            this.lightbox.appendChild(this.nextButton);
            this.lightbox.appendChild(this.closeButton);
            this.wrap.appendChild(this.img);
            
            // accessibility - to do...
            // this.lightbox.tabIndex = -1;
            // this.lightbox.setAttribute('role', 'dialog');
            // this.closeButton.tabIndex = -1;
            // this.closeButton.focus();

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

            this.img.addEventListener('click', function(e) {
                if (e) e.stopPropagation();
            });

            this.prevButton.addEventListener('click', function(e) {
                if (e) e.stopPropagation();
                $this.prev(e);
            });

            this.nextButton.addEventListener('click', function(e) {
                if (e) e.stopPropagation();
                $this.next(e);
            });

            d.addEventListener('keydown', function(e) {
                if ($this.active) {
                    if (e.keyCode == 37) $this.prev(e);
                    if (e.keyCode == 39) $this.next(e);
                    if (e.keyCode == 27) $this.close(e);
                }
            });

            // close lightbox on click
            this.lightbox.addEventListener('click', function(e) {
                $this.close(e);
            });

        },

        update: function() {

            // show/hide lightbox
            if (!this.active) {
                this.lightbox.classList.remove('active');

                // accessibility - to do...
                // this.lightbox.setAttribute('aria-hidden', 'true');
                // this.body.setAttribute('aria-hidden', 'false');
                // this.lastFocus.focus();
                return;
            }

            this.lightbox.classList.add('active');

            // accessibility - to do...
            // this.body.setAttribute('aria-hidden', 'true');

            // hide first/last prev/next buttons
            if (this.currentItem == 0) this.prevButton.classList.add('hidden');
            else this.prevButton.classList.remove('hidden');

            if (this.currentItem == this.galleries[this.currentGallery].length -1) this.nextButton.classList.add('hidden');
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

    MP.Cookie = Cookie;
    MP.Lightbox = SimpleLightbox;
    g.MP = MP;

})(this, document);

MP.ready(function() {

    MP.convertVideoLinksToIframes();

});
