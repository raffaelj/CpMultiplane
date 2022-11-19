<?php

namespace Multiplane\Helper;

class Utils extends \Lime\Helper {

    public function fieldExists($fieldName, $collectionName) {

        static $fieldsPerCollection; // cache

        if (is_null($fieldsPerCollection)) {
            $fieldsPerCollection = [];
        }

        if (isset($fieldsPerCollection[$collectionName])) {
            return isset($fieldsPerCollection[$collectionName][$fieldName]);
        }

        $_collection = $this->app->module('collections')->collection($collectionName);

        if (!$_collection) return null;
        if (!isset($_collection['fields']) || !is_array($_collection['fields'])) return null;

        foreach ($_collection['fields'] as $field) {
            $fieldsPerCollection[$collectionName][$field['name']] = true;
        }

        return isset($fieldsPerCollection[$collectionName][$fieldName]);

    }

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
            $fieldsPerCollection[$collectionName][$field['name']] = isset($field['localize']) && $field['localize'];
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

    public function getTagsList () {

        static $tags; // cache

        if (is_null($tags)) {
            $tags = [];
        } else {
            return $tags;
        }

        $tagsName       = $this->app->module('multiplane')->fieldNames['tags'];
        $publishedName  = $this->app->module('multiplane')->fieldNames['published'];

        $lang           = $this->app->module('multiplane')->lang;
        $defaultLang    = $this->app->module('multiplane')->defaultLang;
        $isMultilingual = $this->app->module('multiplane')->isMultilingual;

        $collections = $this->app->module('multiplane')->use['collections'];

        foreach ($collections as $col) {

            $langSuffix = '';

            if ($isMultilingual) {
                $isFieldLocalized = $this->isFieldLocalized($tagsName, $col);
                if ($isFieldLocalized && ($lang != $defaultLang)) {
                    $langSuffix = '_'.$lang;
                }
            }

            $options = [
                'fields' => [
                    $tagsName => true,
                ],
                'filter' => [
                    $publishedName => true,
                    $tagsName.$langSuffix => [
                        '$ne' => [],
                    ],
                ],
                'lang' => $lang,
            ];
            if ($isMultilingual) {
                $options['fields'][$tagsName.$langSuffix] = true;
            }

            $entries = $this->app->module('collections')->find($col, $options);

            foreach ($entries as $entry) {
                if (isset($entry[$tagsName]) && is_array($entry[$tagsName])) {
                    foreach ($entry[$tagsName] as $tag) {
                        $tags[] = $tag;
                    }
                }
            }

        }

        return array_unique($tags);

    }

}
