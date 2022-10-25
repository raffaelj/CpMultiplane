<?php

namespace Multiplane\Helper;

class Utils extends \Lime\Helper {

    public function isFieldLocalized($fieldName, $collectionName) {

        static $fieldsPerCollection; // cache

        if (is_null($fieldsPerCollection)) {
            $fieldsPerCollection = [];
        }

        if (isset($fieldsPerCollection[$collectionName][$fieldName])) {
            return $fieldsPerCollection[$collectionName][$fieldName];
        }

        $_collection = $this->app->module('collections')->collection($collectionName);

        if (!$_collection) return null;
        if (!isset($_collection['fields']) || !is_array($_collection['fields'])) return null;

        foreach ($_collection['fields'] as $field) {
            $fieldsPerCollection[$collectionName][$fieldName] = isset($field['localize']) && $field['localize'];
        }

        if (isset($fieldsPerCollection[$collectionName][$fieldName])) {
            return $fieldsPerCollection[$collectionName][$fieldName];
        }

        return null;

    }

    public function handleTrailingSlashRoute() {

        if ($this->app->module('multiplane')->disableTrailingSlashRedirect) {
            return;
        }

        $route = $this->app->retrieve('route');

        if (strlen($route) <= 1) return;
        if (substr($route, -1) !== '/') return;

        $newRoute = rtrim($route, '/');

        $status = $this->app->module('multiplane')->statusCodeForTrailingSlashRoutes;

        switch ($status) {

            case 404:   $this->app->response->status = 404;
                        return false;
                        break;
            case 302:   $this->app->reroute($newRoute);
                        break;
            case 301:
            default:    \header('Location: '.$newRoute, true, 301);
                        $this->app->stop();
                        break;
        }

    }
}
