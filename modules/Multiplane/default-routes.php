<?php

// bind routes

// clear cache (only in debug mode)
$this->bind('/clearcache', function() {
    return $this->module('cockpit')->clearCache();
}, $this['debug']);

$this->bind('/login', function() {
    $this->reroute(MP_ADMINFOLDER);
});

$this->bind('/sitemap.xml', function() {
    return $this->invoke('Multiplane\\Controller\\Base', 'sitemap');
});

$this->bind('/getImage', function() {
    return $this->invoke('Multiplane\\Controller\\Base', 'getImage');
});

// routes for live preview
if ($this->module('multiplane')->isPreviewEnabled) {

    $this->bind('/getPreview', function($params) {
        return $this->invoke('Multiplane\\Controller\\CockpitLivePreview', 'getPreview', ['params' => $params]);
    }, $this->req_is('ajax'));

    $this->bind('/livePreview', function($params) {

        if ($this->param('token') != $this->module('multiplane')->livePreviewToken) {
            return false;
        }

        return $this->invoke('Multiplane\\Controller\\CockpitLivePreview', 'livePreview', ['params' => $params]);

    });
}





// bind wildcard routes
$isMultilingual = $this->module('multiplane')->isMultilingual && $this->retrieve('languages', false);

if (!$isMultilingual) {

    $this->module('multiplane')->initI18n($this->module('multiplane')->defaultLang);

    // routes for forms
    $this->bind('/form/*', function($params) {
        return $this->invoke('Multiplane\\Controller\\Forms', 'index', ['params' => $params]);
    });

    // fulltext search
    if ($this->module('multiplane')->get('search/enabled')) {
        $this->bind('/search/*', function($params) {
            return $this->invoke('Multiplane\\Controller\\Base', 'search', ['params' => $params]);
        });
    }

    // tags page
    $this->bind('/tags/*', function($params) {

        $tags = [];
        if (isset($params[':splat'][0]) && $params[':splat'][0] !== '') {
            $tags = explode('/', $params[':splat'][0]);
        }

        return $this->invoke('Multiplane\\Controller\\Base', 'tags', [['tags' => $tags]]);
    });

    $this->bind('/*', function($params) {

        $slug = $params[':splat'][0];

        return $this->invoke('Multiplane\\Controller\\Base', 'index', ['slug' => $slug]);

    });

}
else {

    foreach ($this->module('multiplane')->getLanguages() as $lang) {

        // routes for forms
        $this->bind('/'.$lang.'/form/*', function($params) use($lang) {
            $this->module('multiplane')->initI18n($lang);
            return $this->invoke('Multiplane\\Controller\\Forms', 'index', ['params' => $params]);
        });

        // fulltext search
        if ($this->module('multiplane')->get('search/enabled')) {
            $this->bind('/'.$lang.'/search/*', function($params) use($lang) {
                $this->module('multiplane')->initI18n($lang);
                return $this->invoke('Multiplane\\Controller\\Base', 'search', ['params' => $params]);
            });
        }

        // tags page
        $this->bind('/'.$lang.'/tags/*', function($params) use($lang) {
            $this->module('multiplane')->initI18n($lang);

            $tags = [];
            if (isset($params[':splat'][0]) && $params[':splat'][0] !== '') {
                $tags = explode('/', $params[':splat'][0]);
            }

            return $this->invoke('Multiplane\\Controller\\Base', 'tags', [['tags' => $tags]]);
        });

        $this->bind('/'.$lang.'/*', function($params) use($lang) {

            $this->module('multiplane')->initI18n($lang);
            return $this->invoke('Multiplane\\Controller\\Base', 'index', ['slug' => ($params[':splat'][0] ?? '')]);

        });

    }

    // redirect "/" to "/en"
    $this->bind('/', function() {

        $defaultLang = $this->module('multiplane')->defaultLang;

        $lang = $this->getClientLang($defaultLang);

        if (!in_array($lang, $this->module('multiplane')->getLanguages())) {
            $lang = $defaultLang;
        }
        $this->reroute('/' . $lang );

    });

}
