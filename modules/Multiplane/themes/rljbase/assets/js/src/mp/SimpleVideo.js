
var d  = document,
    MP = require('./MP.js');

module.exports = {

    // deprecated, but still necessary for video links in wysiwyg field
    convertVideoLinksToIframes: function() {

        var $this = this,
            video_links = d.querySelectorAll('a[data-video-id]');

        Array.prototype.forEach.call(video_links, function(el, i) {

            var id       = el.getAttribute('data-video-id'),
                provider = el.getAttribute('data-video-provider'),
                asset    = el.getAttribute('data-video-thumb'),
                width    = 480,
                height   = 370,
                ratio    = '',
                thumb    = '',
                src      = '';

            if ((data_width = el.getAttribute('data-video-width'))
                && (data_height = el.getAttribute('data-video-height'))) {

                // reassign aspect ratio
                height = width * (data_height / data_width);
                ratio  = (data_width / data_height == 16 / 9) ? '16-9' : '4-3';

            }

            // check, if asset starts with "http" or contains "."
            if (asset.indexOf('http') == 0 || asset.indexOf('.') != -1) {
                thumb = asset; // full image url
            } else {           // asset id
                thumb = MP.base_url + '/getImage?src=' + asset + '&w=480&o=1';
            }

            if (provider == 'youtube') {

                // downloaded thumbnails are always 4:3 (640px x 480px) with black borders
                // lazy fix: overwrite ratio
                ratio = '16-9';

                src = 'https://www.youtube-nocookie.com/embed/'
                    + id + '?enablejsapi=1&rel=0&showinfo=0&autoplay=1';

            }

            if (provider == 'vimeo') {
                src = 'https://player.vimeo.com/video/'
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
            iframe.setAttribute('allowfullscreen', '');
            iframe.setAttribute('title', 'Video');
            iframe.style['background-image'] = 'url(' + thumb + ')';

            if (!iframe.getAttribute('id')) {
                iframe.setAttribute('id', 'player_' + Math.random().toString(36).substring(2));
            }

            iframe.tabIndex = -1;

            container.appendChild(iframe);

            var play_button = d.createElement('a');
            play_button.setAttribute('class', 'icon-play');
            play_button.setAttribute('href', '#');
            play_button.setAttribute('aria-label', 'Play');
            play_button.tabIndex = 0;

            container.appendChild(play_button);

            el.parentNode.insertBefore(container, el);

            play_button.addEventListener('click', function(e) {

                if (e) e.preventDefault();

                // fire event to check for privacy cookie
                MP.trigger('privacy', {
                    type:   'external_video',
                    target: iframe,
                    cookie: 'loadExternalVideos',
                    event: 'external_video'
                });

            });

        });

        MP.on('external_video', function(e) {

            if (e && e.params && e.params.target) {
                $this.loadVideo(e.params.target);
            }

        });

    },

    replaceVideoLink: function() {

        var $this = this,
            video_links = d.querySelectorAll('.video_embed');

        Array.prototype.forEach.call(video_links, function(iframe, i) {

            iframe.tabIndex = -1;

            if (!iframe.getAttribute('id')) {
                iframe.setAttribute('id', 'player_' + Math.random().toString(36).substring(2));
            }

            var play_button = iframe.nextElementSibling;

            play_button.addEventListener('click', function(e) {

                if (e) e.preventDefault();

                // fire event to check for privacy cookie
                MP.trigger('privacy', {
                    type:   'external_video',
                    target: iframe,
                    cookie: 'loadExternalVideos',
                    event: 'external_video'
                });

            });

        });

        MP.on('external_video', function(e) {

            if (e && e.params && e.params.target) {
                $this.loadVideo(e.params.target);
            }

        });

    },

    loadVideo: function(iframe) {

        var $this  = this,
            id     = iframe.getAttribute('id')
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
    
}
