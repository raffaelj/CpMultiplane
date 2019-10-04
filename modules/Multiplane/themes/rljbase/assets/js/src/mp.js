
var g = window, d = document;

// for relative paths - MP_BASE_URL should be declared in the head of your
// template file: <script>var MP_BASE_URL = '{{ MP_BASE_URL }}';</script>
if (typeof g.MP_BASE_URL === 'undefined') {
    g.MP_BASE_URL = ''; // to do: try to guess url
}

// polyfills
if (!(g.Promise && g.FormData)) {

    if (typeof g.MP_POLYFILLS_URL === 'undefined') {
        g.MP_POLYFILLS_URL = ''; // to do: try to guess url
    }

    var js = d.createElement('script');
    js.src = MP_POLYFILLS_URL;
    d.head.appendChild(js);

}

module.exports = {

    base_route : MP_BASE_URL,
    base_url   : MP_BASE_URL,

    route: function(url) {
        return this.base_route+url;
    },

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

    // deprecated, but still necessary for video links in wysiwyg field
    convertVideoLinksToIframes: function() {
        return this.Video.convertVideoLinksToIframes();
    },

    replaceVideoLink: function() {
        return this.Video.replaceVideoLink();
    },

    Cookie   : require('./mp/Cookie.js'),
    Carousel : require('./mp/SimpleCarousel.js'),
    Lightbox : require('./mp/SimpleLightbox.js'),
    Video    : require('./mp/SimpleVideo.js'),

};
