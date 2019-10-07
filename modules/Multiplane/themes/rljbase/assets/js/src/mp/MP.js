
var d = document;

// polyfills
if (!(window.Promise && window.FormData)
  && typeof MP_POLYFILLS_URL != 'undefined') {
    var js = d.createElement('script');
    js.src = MP_POLYFILLS_URL;
    d.head.appendChild(js);
}

module.exports = {

    base_route : typeof MP_BASE_URL != 'undefined' ? MP_BASE_URL : '',
    base_url   : typeof MP_BASE_URL != 'undefined' ? MP_BASE_URL : '',
    _events    : {},

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

    // source: https://github.com/agentejo/cockpit/blob/next/assets/app/js/app.js
    // Cockpit CMS, Artur Heinze, MIT License
    on: function(name, fn){
        if (!this._events[name]) this._events[name] = [];
        this._events[name].push(fn);
    },

    // source: https://github.com/agentejo/cockpit/blob/next/assets/app/js/app.js
    // Cockpit CMS, Artur Heinze, MIT License
    off: function(name, fn){
        if (!this._events[name]) return;

        if (!fn) {
           this._events[name] = [];
        } else {

            for (var i=0; i < this._events[name].length; i++) {
                if (this._events[name][i]===fn) {
                    this._events[name].splice(i, 1);
                    break;
                }
            }
        }
    },

    // source: https://github.com/agentejo/cockpit/blob/next/assets/app/js/app.js
    // Cockpit CMS, Artur Heinze, MIT License
    trigger: function(name, params) {

        if (!this._events[name]) return;

        var event = {"name":name, "params": params};

        for (var i=0; i < this._events[name].length; i++) {
            this._events[name][i].apply(MP, [event]);
        }
    },

};
