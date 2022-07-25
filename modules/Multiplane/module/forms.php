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
        'error_generic' => 'Something went wrong',
    ],

    'getFormFields' => function($form = '', $options = []) {

        if (empty($form)) $form = $this->contact;

        $_form = $this->app->module('forms')->form($form);
        if (!$_form) return false;

        $fields = $_form['fields'] ?? null;
        if (empty($fields)) return false;

        $response = $this('session')->read("mp_form_response_$form", []);

        foreach($fields as &$field) {

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

            // set attributes
            $attr = $this->resolveFormFieldAttributes($field, $form);
            $field['attr'] = $attr[0];
            $field['aria'] = $attr[1];

        }

        return $fields;

    },

    'resolveFormFieldAttributes' => function($field, $form = '') {

        $prefix = $this->formIdPrefix;

        $attr['name'] = $field['name'];
        $attr['id']   = "{$prefix}{$form}_{$field['name']}";

        $ariaDescribedBy = [];
        if (!empty($field['info']))  $ariaDescribedBy['info'] = $attr['id'] . '_aria_info';
        if (!empty($field['link']))  $ariaDescribedBy['link'] = $attr['id'] . '_aria_linkinfo';
        if (!empty($field['error'])) $ariaDescribedBy['error'] = $attr['id'] . '_aria_error';

        if (!empty($ariaDescribedBy)) {
            $attr['aria-describedby'] = join(' ', $ariaDescribedBy);
        }

        if (isset($field['required']) && $field['required']) {
            $attr['required'] = true;
        }

        // may overwrite id and name
        if (isset($field['options']['attr'])) {
            foreach($field['options']['attr'] as $key => $val) {
                $attr[$key] = $val;
            }
        }

        $attr['name'] = "{$prefix}{$form}[{$attr['name']}]";

        return [$attr, $ariaDescribedBy];

    },

    // helper function to convert array to html attribute string
    'arrayToAttributeString' => function($attr) {
        return $this->getHtmlAttributesFromArray($attr);
    },

    // helper function to convert array to html attribute string
    'getHtmlAttributesFromArray' => function($attr) {

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

        // error from mailer
        if (is_string($response['error'])) {
            if ($this->app['debug']) {
                return $response['error'];
            }
            else {
                $mailerMessage = $this->formMessages['mailer'];

                $_form = $this->app->module('forms')->form($form);
                $customMailerMassage = $_form['formMessages']['mailer'] ?? null;

                if ($customMailerMassage && is_string($customMailerMassage) && !empty(trim($customMailerMassage))) {
                    $mailerMessage = $customMailerMassage;
                }

                return $mailerMessage;
            }

        }

        // error from validator or honeypot
        // normal fields have their own error massage/handling
        $out = '';
        foreach (['validator', 'honeypot'] as $key) {
            if (!isset($response['error'][$key])) continue;
            $val = $response['error'][$key];
            $out .= "<strong>$key:</strong> ";
            $out .= \is_string($val) ? $val : \implode('<br>', $val);
            $out .= '<br>';
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
