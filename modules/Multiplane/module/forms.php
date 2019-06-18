<?php

// forms helper
$this->helpers['form'] = 'Multiplane\\Controller\\Forms'; // for widget usage

$this->module('multiplane')->extend([

    'comments' => 'comments',     // comment form name
    'contact' => 'contact',       // contact form name
    'newsletter' => 'newsletter', // newsletter form name
    'hasCommentSection' => false,
    'hasContactForm' => false,

    'currentFormId' => 'contact',

    'formSessionName' => md5(__DIR__),
    'formSessionExpire' => 30,

    'formIdPrefix' => 'mp_form_',
    'formSubmitButtonName' => 'submit',

    'formSendReferer' => false,

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

        $response = $this('session')->read('mp_form_response', []);

        foreach($fields as &$field) {

            // set attributes
            $field['attr'] = $this->resolveFormFieldAttributes($field);

            // set/get values
            $field['value'] = $response['data'][$field['name']] ?? '';

            // linked item, e. g. link to privacy notice page
            if (isset($field['options']['link']['_id']) && isset($field['options']['link']['link'])) {
                $field['link'] = $this->resolveLinkedItem($field['options']['link']);
            }

            // add error messages
            if (isset($response['error'][$field['name']])) {

                $error = $response['error'][$field['name']];
                $field['error'] = is_string($error) ? $error : implode('<br>', $error);

            }
            if (isset($field['options']['attr']['name']) && isset($response['error'][$field['options']['attr']['name']])) {

                $error = $response['error'][$field['options']['attr']['name']];
                $field['error'] = is_string($error) ? $error : implode('<br>', $error);

            }

        }

        return $fields;

    },

    'resolveFormFieldAttributes' => function($field) {

        $attr['name'] = $attr['id'] = $this->formIdPrefix . $field['name'];

        if(isset($field['required']) && $field['required']) {
            $attr['required'] = 'required';
        }

        if(isset($field['options']['attr'])) {
            foreach($field['options']['attr'] as $key => $val) {
                $attr[$key] = $val;
            }
        }

        return $attr;

    },

    // helper function to convert array to html attribute string
    'arrayToAttributeString' => function($array) {

        $attributes = '';

        foreach($array as $key => $val) {

            $attributes .= ' '.$key.'="'.$val.'"';

        }

        return $attributes;

    },

    'formatErrorMessage' => function() {

        $response = $this('session')->read('mp_form_response', null);

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
                $out .= is_string($val) ? $val : implode('<br>', $val);
            }

        }
        return $out;

    },

    'resolveLinkedItem' => function($link) {

        $filter = [
            '_id' => $link['_id'],
        ];

        $lang = $this('i18n')->locale;

        if ($lang == $this->defaultLang) {
            $projection = [
                'title' => true,
                $this->slugName => true,
                '_id' => false,
            ];

            $linkedItem = $this->app->module('collections')->findOne($link['link'], $filter, $projection, null, false, ['lang' => $lang]);
        }

        else {
            $projection = [
                'title' => true,
                'title_'.$lang => true,
                $this->slugName => true,
                $this->slugName . '_'. $lang => true,
                '_id' => false,
            ];

            $linkedItem = $this->app->module('collections')->findOne($link['link'], $filter, $projection, null, false, ['lang' => $lang]);

            $linkedItem = $this->app->module('collections')->_filterFields($linkedItem, $link['link'], ['lang' => $lang]);
        }

        foreach($linkedItem as $k => $v) $link[$k] = $v;

        return $link;

    },

]);
