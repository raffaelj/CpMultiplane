<?php

// to do: cleanup, move html helper functions to a helper class...

// forms helper for widget usage
$this->helpers['form'] = 'Multiplane\\Controller\\Forms';

$this->module('multiplane')->extend([

    // 'comments'              => 'comments',      // comment form name - to do...
    'contact'               => 'contact',       // contact form name
    // 'newsletter'            => 'newsletter',    // newsletter form name - to do...
    // 'hasCommentSection'     => false,           // to do...
    // 'hasContactForm'        => false,           // to do...

    'formSessionName'       => \md5(__DIR__),
    'formSessionExpire'     => 30,              // time in seconds

    'formIdPrefix'          => 'mp_form_',      // for form fields to prevent doubled ids
    'formSubmitButtonName'  => 'submit',        // prevent form validator from validating the button

    'formSendReferer'       => false,           // send current page with contact form

    'formStandalone'        => true,            // forms are accessible as stand-alone page via /form/form_name

    'formMessages' => [
        'success' => 'Thank you for your message. I will answer soon.',
        'notice'  => 'Please fill in all mandatory fields correctly.',
        'mailer'  => 'Your mail wasn\'t sent correctly.',
    ],

    'getFormFields' => function($form = '', $options = []) {

        if (empty($form)) $form = $this->contact;

        $_form = $this->app->module('forms')->form($form);
        if (!$_form) return false;

        $fields = $_form['fields'] ?? null;
        if (empty($fields)) return false;

        $response = $this('session')->read("mp_form_response_$form", []);

        foreach($fields as &$field) {

            // set attributes
            $field['attr'] = $this->resolveFormFieldAttributes($field, $form);

            // set/get values
            $field['value'] = $response['data'][$field['name']] ?? '';

            // linked item, e. g. link to privacy notice page
            if (isset($field['options']['link']['_id']) && isset($field['options']['link']['link'])) {
                $field['link'] = $this->resolveLinkedItem($field['options']['link']);
            }

            // add error messages
            if (isset($response['error'][$field['name']])) {

                $error = $response['error'][$field['name']];
                $field['error'] = \is_string($error) ? $error : \implode('<br>', $error);

            }
            if (isset($field['options']['attr']['name']) && isset($response['error'][$field['options']['attr']['name']])) {

                $error = $response['error'][$field['options']['attr']['name']];
                $field['error'] = \is_string($error) ? $error : \implode('<br>', $error);

            }

        }

        return $fields;

    },

    'resolveFormFieldAttributes' => function($field, $form = '') {

        $attr['name'] = $field['name'];
        $attr['id']   = "{$this->formIdPrefix}{$form}_{$field['name']}";

        if (isset($field['required']) && $field['required']) {
            $attr['required'] = true;
        }

        // may overwrite id and name
        if (isset($field['options']['attr'])) {
            foreach($field['options']['attr'] as $key => $val) {
                $attr[$key] = $val;
            }
        }

        $attr['name'] = $form . '[' . $attr['name'] . ']';

        return $attr;

    },

    // helper function to convert array to html attribute string
    'arrayToAttributeString' => function($attr) {

        $attributes = '';

        foreach ($attr as $key => $val) {

            if (is_bool($val) && $val === true) {
                $attributes .= ' '.$key;
                continue;
            }

            $attributes .= ' '.$key.'="'.$val.'"';

        }

        return $attributes;

    },

    'formatErrorMessage' => function($form = '') {

        $response = $this('session')->read("mp_form_response_$form", null);

        if (!isset($response['error'])) return false;

        if (is_string($response['error'])) {
            // error from mailer
            return $this->app['debug'] ? $response['error'] : $this->formMessages['mailer'];
        }

        // possible keys: 'validator', 'honeypot' (and field names - not needed here)

        $out = '';
        foreach ($response['error'] as $key => $val) {

            if ($key != 'validator' && $key != 'honeypot') continue;

            else {
                $out .= "<strong>$key: </strong><br>";
                $out .= \is_string($val) ? $val : \implode('<br>', $val);
            }

        }
        return $out;

    },

    'resolveLinkedItem' => function($link) {

        $slugName      = $this->fieldNames['slug'];
        $titleName     = $this->fieldNames['title'];

        $filter = [
            '_id' => $link['_id'],
        ];

        $lang = $this->lang;

        if ($lang == $this->defaultLang) {
            $projection = [
                $titleName => true,
                $slugName  => true,
                '_id'      => false,
            ];

            $linkedItem = $this->app->module('collections')->findOne($link['link'], $filter, $projection, null, false, ['lang' => $lang]);
        }

        else {
            $projection = [
                $titleName             => true,
                "{$titleName}_{$lang}" => true,
                $slugName              => true,
                "{$slugName}_{$lang}"  => true,
                '_id'                  => false,
            ];

            $linkedItem = $this->app->module('collections')->findOne($link['link'], $filter, $projection, null, false, ['lang' => $lang]);

            $linkedItem = $this->app->module('collections')->_filterFields($linkedItem, $link['link'], ['lang' => $lang]);
        }

        if (!$linkedItem) return false;

        foreach ($linkedItem as $k => $v) $link[$k] = $v;

        return $link;

    },

]);
