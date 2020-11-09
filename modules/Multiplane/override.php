<?php

// adjust some auto-detected directory routes to current dir, otherwise inbuilt
// functions from Lime\App, like pathToUrl() would return wrong paths
$this->set('docs_root',  MP_DOCS_ROOT);
$this->set('base_url',   MP_BASE_URL);
$this->set('base_route', MP_BASE_URL); // for reroute()
$this->set('site_url',   $this->getSiteUrl(true)); // for pathToUrl(), which is used in thumbnail function

if (class_exists('\Lime\Request') && !COCKPIT_CLI) {
    // $this->request->site_url   = $this['site_url'];
    $this->request->base_url   = $this['base_url'];
    $this->request->base_route = $this['base_route'];
}

// rewrite filestorage paths to get correct image urls
$this->on('cockpit.filestorages.init', function(&$storages) {
    $storages['uploads']['url'] = $this->pathToUrl('#uploads:', true);
    $storages['thumbs']['url']  = $this->pathToUrl('#thumbs:', true);
});

// use a modified renderer, that extends the core Lexy class
$lexy = $this->renderer;
$this->service('renderer', function() use ($lexy) {

    $renderer = new \MPLexy($lexy);

    $renderer->debug = $this->retrieve('multiplane/debug/lexy', false);

    // remove some white space to prettify the html output
    $renderer->after(function($content) {
        return \preg_replace('/([\r\n])(\s*)\<\?php(?!\s*(echo|\$app->trigger))/', '$1<?php', $content);
    });

    return $renderer;
});

// add debug overlay
if ($this->debug) {
    $this->on('multiplane.init', function() {
        if ($this->retrieve('multiplane/debug/overlay', false)) {
            $this->on('multiplane.layout.contentafter', function() {
                $this->renderView('views:partials/debug-overlay.php');
            });
        }
    });
}

// set global viewvars for template files - they will be filled later, but they must be available
$this->viewvars['page']       = [];
$this->viewvars['site']       = [];
$this->viewvars['posts']      = [];
$this->viewvars['pagination'] = [];
$this->viewvars['_meta']      = [];

// error handling
$this->on('after', function() {

    // force 404 if body is empty
    if (!$this->response->body || $this->response->body === 404) {
        $this->response->status = 404;
    }
 
    if ($this->module('multiplane')->isInMaintenanceMode) {

        if (!$this->module('multiplane')->clientIpIsAllowed) {
            $this->response->status = 503;
        }

    }

    switch($this->response->status){
        case '404':
            $this->response->body = $this->invoke('Multiplane\\Controller\\Base', 'error', ['status' => $this->response->status]);
            break;
        case '503':
            $this->response->headers[] = 'Retry-After: 3600';
            $this->response->body = $this->invoke('Multiplane\\Controller\\Base', 'error', ['status' => $this->response->status]);
            break;
    }

});
