(function(g, d) {

    // for relative paths - MP_BASE_URL should be declared in the head of your
    // template file: <script>var MP_BASE_URL = '{{ MP_BASE_URL }}';</script>
    if (!MP_BASE_URL) var MP_BASE_URL = '';

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

    MP.Cookie = Cookie;
    g.MP = MP;

})(this, document);

MP.ready(function() {

    MP.convertVideoLinksToIframes();

});
