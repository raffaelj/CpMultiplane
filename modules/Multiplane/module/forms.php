<?php

// TODO: cleanup, move html helper functions to a helper class...

// forms helper for widget usage
$this->helpers['form'] = 'Multiplane\\Controller\\Forms';

/**
 * Fire event with priority 101 (1 higher, than the actual form validation
 * from the FormValidation addon) and pass $_FILES array to $data
 * 
 * This also fixes the validator firing too early if file field is required.
 */
$this->on('forms.submit.before', function($form, &$data, $frm, &$options) {

    $fileFields = [];

    $formFields = $frm['fields'] ?? [];
    foreach ($formFields as $v) {
        if ($v['type'] == 'file') $fileFields[] = $v;
    }

    // if the form has no file upload fields, skip all the steps below
    if (empty($fileFields)) return;

    $prefix = $this->module('multiplane')->formIdPrefix;

    foreach ($fileFields as $field) {

        $files = $this->module('formvalidation')->getUploadedFiles($prefix.$form, $field['name'], false);

        if (!empty($files)) $data[$field['name']] = $files;

    }

    /**
     * Add uploaded files to assets
     */
    $this->on('forms.validate.after', function($form, &$data, $frm, &$options) use($fileFields) {

        if (isset($frm['save_uploaded_assets']) && $frm['save_uploaded_assets']) {

            $folderName = $this->module('formvalidation')->formsUploadsFolder;
            $folder = $this->module('formvalidation')->getFormsUploadsFolder($folderName.'/'.$frm['name']);

            foreach ($fileFields as $field) {

                $files = $data[$field['name']] ?? [];

                if (empty($files)) continue;

                // temporary disable ImageResize addon
                if (isset($this['modules']['imageresize'])) {
                    $origImageResizeConfig = $this->module('imageresize')->getConfig();
                    $newConfig = $origImageResizeConfig;
                    $newConfig['resize'] = false;
                    $newConfig['optimize'] = false;
                    $newConfig['profiles'] = null;
                    $this->module('imageresize')->config = $newConfig;
                    if (isset($this['modules']['cpmultiplanegui'])) {
                        $origImageResizeAutoConfig = $this->module('cpmultiplanegui')->hasAutoConfigImageResize;
                        $this->module('cpmultiplanegui')->hasAutoConfigImageResize = true;
                    }
                }

                // temporary change max_upload_size
                $origMaxUploadSize = $this->retrieve('max_upload_size');
                $fieldMaxUploadSize = $field['options']['max_upload_size'] ?? 0;
                if ($fieldMaxUploadSize) $this->set('max_upload_size', $fieldMaxUploadSize);

                // temporary change allowed_uploads
                $origAllowedUploads = $this->retrieve('allowed_uploads');
                $fieldAllowedUploads = $field['options']['allowed_uploads'] ?? '';
                if (!empty($fieldAllowedUploads)) $this->set('allowed_uploads', $fieldAllowedUploads);

                // Get response from uploaded assets
                $assets = $this->module('cockpit')->uploadAssets($files, ['folder' => $folder]);

                // restore max_upload_size and allowed_uploads
                $this->set('max_upload_size', $origMaxUploadSize);
                $this->set('allowed_uploads', $origAllowedUploads);

                // restore ImageResize config
                if (isset($this['modules']['imageresize'])) {
                    $this->module('imageresize')->config = $origImageResizeConfig;
                    if (isset($this['modules']['cpmultiplanegui'])) {
                        $this->module('cpmultiplanegui')->hasAutoConfigImageResize = $origImageResizeAutoConfig;
                    }
                }

                // save entries as filename
                // $data[$field['name']] = $assets['uploaded'];

                // save entries as filepath
                // $data[$field['name']] = [];
                // $ASSETS_URL    = rtrim($this->filestorage->getUrl('assets://'), '/');

                // foreach ($assets['assets'] as $file) {
                //     $data[$field['name']][] = $ASSETS_URL.$file['path'];
                // }

                // save entries as assets data
                $data[$field['name']] = $assets['assets'];

            }

        }

        /**
         * Add uploaded files as mail attachment
         * 
         */
        $this->on('forms.submit.email', function($form, &$data, $frm, &$body, &$options) use ($fileFields) {

            $attachUploadsToMail = isset($frm['attach_uploaded_assets']) && $frm['attach_uploaded_assets'] === true;

            if (!$attachUploadsToMail) return;

            $options['attachments'] = $options['attachments'] ?? [];

            $ASSETS_URL = rtrim($this->filestorage->getUrl('assets://'), '/');

            foreach ($fileFields as $field) {

                $files = $data[$field['name']] ?? [];

                if (empty($files)) continue;

                $isDataFromAsset = isset($files[0]['path']);
                $isDataFromFiles = isset($files['name']) && isset($files['tmp_name']);

                // reset data key
                $data[$field['name']] = [];

                if ($isDataFromFiles) {

                    foreach ($files['name'] as $k => $v) {

                        $tmpFileName = $v;
                        $tmpFilePath = $files['tmp_name'][$k];

                        // TODO: Sanitize svg before adding it as attachment
                        // if (\preg_match('/\.(svg|xml)$/i', $tmpFileName)) {
                        //     file_put_contents($tmpFilePath, \SVGSanitizer::clean(\file_get_contents($tmpFilePath)));
                        // }

                        // pass array to attachments --> needs modified Mailer class
                        $options['attachments'][] = [
                            'path' => $tmpFilePath,
                            'name' => $tmpFileName,
                        ];

                        // add file name to data key
                        $data[$field['name']][] = $v;
                    }
                }
                elseif ($isDataFromAsset) {

                    foreach ($files as $asset) {

                        if ($path = $this->path('#uploads:'.ltrim($asset['path'], '/'))) {
                            $options['attachments'][] = $path;
                        }
                        else {
                            // TODO: use filestorage api and write stream
                        }

                        // add url to data key
                        $data[$field['name']][] = $ASSETS_URL.$asset['path'];

                    }
                }
            }

        });

    });

}, 101);



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

        foreach ($fields as &$field) {

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

            // add file size info to file upload fields
            if ($field['type'] == 'file') {
                $max_upload_size = !empty($field['options']['max_upload_size']) ? $field['options']['max_upload_size'] : $this->app->retrieve('max_upload_size', 0);
                $formattedSize = $this->app->helper('i18n')->get('max:') . ' ' . $this->app->helper('utils')->formatSize($max_upload_size);
                $field['info'] = ($field['info'] ?? '') . " ({$formattedSize})";
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
        if (!empty($field['info']))  $ariaDescribedBy['info']  = $attr['id'] . '_aria_info';
        if (!empty($field['link']))  $ariaDescribedBy['link']  = $attr['id'] . '_aria_linkinfo';
        if (!empty($field['error'])) $ariaDescribedBy['error'] = $attr['id'] . '_aria_error';

        if (!empty($ariaDescribedBy)) {
            $attr['aria-describedby'] = join(' ', $ariaDescribedBy);
        }

        if (isset($field['required']) && $field['required']) {
            $attr['required'] = true;
        }

        // may overwrite id and name
        if (isset($field['options']['attr']) && is_array($field['options']['attr'])) {
            foreach($field['options']['attr'] as $key => $val) {
                $attr[$key] = $val;
            }
        }

        // set atributes for file upload
        if ($field['type'] == 'file') {
            // set multiple attribute
            if (isset($field['options']['multiple']) && is_bool($field['options']['multiple'])) {
                $attr['multiple'] = $field['options']['multiple'];
            }
            // set accept attribute
            if ($allowed = $field['options']['allowed_uploads'] ?? null) {
                if (!empty($allowed) && $allowed != '*') {
                    if (is_string($allowed)) $allowed = explode(',', $allowed);
                    $allowed = array_map(function($v) {return '.'.trim($v);}, $allowed);
                    $attr['accept'] = implode(',', $allowed);
                }
            }
        }

        // apply form prefix to name attribute
        $attr['name'] = "{$prefix}{$form}[{$attr['name']}]";

        if (($field['type'] == 'file') && isset($attr['multiple']) && $attr['multiple'] === true) {
            $attr['name'] .= '[]';
        }

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

            if (is_bool($val)) {
                if ($val === true) $attributes .= ' '.$key;
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
