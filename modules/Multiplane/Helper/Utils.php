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

}
