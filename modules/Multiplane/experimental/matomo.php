<?php

$this->on('multiplane.init', function() {

    $matomo = $this->retrieve('multiplane/matomo', false);

    if (!$matomo || !is_array($matomo)) return;

    $url    = $matomo['url']    ?? false;
    $id     = $matomo['id']     ?? false;
    $cookie = $matomo['cookie'] ?? 'allowMatomoTracking';

    if (!$url || !$id) return;

    // add checkbox for matomo to privacy popup
    $this->on('multiplane.privacy.form', function() {

        $matomo    = $this->retrieve('multiplane/matomo', false);
        $preselect = $matomo['id']     ?? false;
        $cookie    = $matomo['cookie'] ?? 'allowMatomoTracking';

        echo '<input id="'.$cookie.'" name="'.$cookie.'" type="checkbox" value="1"'.($preselect ? ' checked' : '').' />';
        echo '<label for="'.$cookie.'">';
        echo $this('i18n')->get('Allow collecting user statistics with Matomo');
        echo '</label>';

    });

    // matomo analytics
    mp()->add('scripts', [

// force setting a "zero cookie" to avoid asking again
'MP.on("privacy.form.submit", function(e) {
    e.params.set("'.$cookie.'", e.params.has("'.$cookie.'") ? "1" : "0");
});',

// trigger "privacy" event if no "zero cookie" is present
'MP.ready(function() {
    if (MP.Cookie.get("'.$cookie.'") != "0") {
        MP.trigger("privacy", {
            type:   "tracking",
            event:  "matomo",
            cookie: "'.$cookie.'"
        });
    }
});',

// untick matomo checkbox if it was disabled and popup opens again (e. g. for video)
'MP.on("privacy.show", function() {
    if (MP.Cookie.get("'.$cookie.'") == "0") {
        (document.getElementById("'.$cookie.'")).checked = false;
    }
});',

// insert tracking code when "matomo" event is fired
// pass var _paq to window, because we are inside a function scope now
// and copy/paste matomo tracking code below
'MP.on("matomo", function() {

    window._paq = window._paq || [];

    var _paq = window._paq || [];
    _paq.push(["trackPageView"]);
    _paq.push(["enableLinkTracking"]);
    (function() {
      var u="'.$url.'";
      _paq.push(["setTrackerUrl", u+"matomo.php"]);
      _paq.push(["setSiteId", "'.$id.'"]);
      var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0];
      g.type="text/javascript"; g.async=true; g.defer=true; g.src=u+"matomo.js"; s.parentNode.insertBefore(g,s);
    })();

});'
    ]);
    
}, 100);
