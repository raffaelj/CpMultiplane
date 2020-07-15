
<span id="live-preview"></span>

<script>

    var previewScripts = {{ mp()->previewScripts ? 'true' : 'false' }};
    var previewMethod  = '{{ mp()->previewMethod }}';
    var previewDelay   = '{{ mp()->previewDelay ?? 0 }}';

    var firstRun = true;

    function livePreview(event) {

// console.log(event.data.entry);

        firstRun = false;

        MP.request('/getPreview', event.data, 'html').then(function(data) {

            if (previewMethod == 'html') {

                document.getElementById('live-preview').innerHTML = data;

                if (previewScripts) {
                    MP.ready(function() {
                        MP.replaceVideoLink();
                        MP.convertVideoLinksToIframes();
                        MP.Lightbox.init({group:".gallery",selector:"a"});
                        MP.Carousel.init({selector:".carousel"});
                    });
                }

            }

            else if (previewMethod == 'json') {
                // to do...

                // document.getElementById("title").textContent = entry.title;
                // document.getElementById("title").textContent = entry['title' + (lang != 'default' ? '_'+lang : '')];

                document.getElementById('live-preview').textContent = JSON.stringify(data);
            }

        });

    };

    window.addEventListener('message', function(event) {

        /**
         * source: cockpit/modules/Collections/assets/collection-entrypreview.tag
         *
         * var data = {
         *     'event': 'cockpit:collections.preview',
         *     'collection': this.collection.name,
         *     'entry': this.entry,
         *     'lang': this.lang || 'default'
         * };
         *
         */

        // optional: set a timeout to prevent massive requests while typing
        // default: 0
        if (firstRun) livePreview(event);

        else setTimeout(function() {livePreview(event);}, previewDelay);

    }, false);

</script>
