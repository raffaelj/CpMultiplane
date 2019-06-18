<?php

namespace Multiplane\Controller;

class Forms extends \LimeExtra\Controller {

    public function index($params = []) {

        if (!empty($params[':splat'][0])) {

            $slug = $params[':splat'][0];

            if (strpos($slug, '/')) {

                $parts = explode('/', $slug);

                if ($parts[0] == 'submit' && !empty($parts[1])) {
                    return $this->submit($parts[1]);
                }

            }

            $form = $params[':splat'][0];
            return $this->form($form);
        }

        return false;

    }

    public function form($form = '', $options = []) {

        $sessionName = $this->app->module('multiplane')->formSessionName;

        $this('session')->init($sessionName);

        $fields = $this->app->module('multiplane')->getFormFields($form);

        if (!$fields) return false;

        $page = []; // if form is a standalone page

        $success = false;
        $notice  = false;

        // hide messages if session is expired and user calls the url again
        $expire = $this->app->module('multiplane')->formSessionExpire;

        $call = $this('session')->read('mp_form_call', null);

        if (!$call || ($call && (time() - $call > $expire))) {

            $this('session')->destroy();

            $success = false;
            $notice  = false;

        }
        
        
        if ($this('session')->read('mp_form_notice', false)) {
            $notice = true;
        }

        if ($this('session')->read('mp_form_success', false)) {
            $success = true;
        }

        $message = [
            'success' => $success ? $this->app->module('multiplane')->formMessages['success'] : '',
            'notice'  => $notice  ? $this->app->module('multiplane')->formMessages['notice']  : '',
            'error'   => $this->app->module('multiplane')->formatErrorMessage(),
        ];

        return $this->render('views:partials/form.php', compact('page', 'form', 'fields', 'message', 'options'));

    }

    public function submit($form = '') {

        $sessionName = $this->app->module('multiplane')->formSessionName;

        $this('session')->init($sessionName);
        $this('session')->write('mp_form_call', time());

        $referer = !empty($_SERVER['HTTP_REFERER']) ? parse_url(htmlspecialchars($_SERVER['HTTP_REFERER'])) : null;

        if (!$referer) {
            // might be disabled, use a default fallback
            // to do...
            $path = $this->app['site_url'] . '/form/contact';
            $referer = parse_url($path);
        }

        $refererUrl = @$referer['scheme'] . '://' .  @$referer['host'] . @$referer['path'];

        if (mb_stripos($refererUrl, $this->app['site_url']) !== 0) {

            // submitting data from somewhere else is not allowed, but
            // HTTP_REFERER could be faked
            // to do: Cors...
            return ['error' => 'submitting data from somewhere else is not allowed'];

        }

        // cast user input and remove optional id prefix before sending it to validator
        $postedData = [];
        $prefix = $this->app->module('multiplane')->formIdPrefix;
        $formSubmitButtonName = $this->app->module('multiplane')->formSubmitButtonName;

        $strlen = strlen($prefix);
        foreach($_POST as $key => $val) {
            if ($key == $formSubmitButtonName) continue;

            if (substr($key, 0, $strlen) == $prefix) {
                $k = substr($key, $strlen);
            } else {$k = $key;}

            $postedData[$k] = htmlspecialchars(trim($val));
        }
        
        if ($this->app->module('multiplane')->formSendReferer) {
            $postedData['referer'] = $refererUrl;
        }

        // catch response stop from FormValidation addon
        try {
            $response = $this->module('forms')->submit($form, $postedData);
        } catch (\Exception $e) {
            $response = json_decode($e->getMessage(), true);
        }

        if (!isset($response['error'])) {
            $this('session')->delete('mp_form_response');
            $this('session')->delete('mp_form_notice');
            
            $this('session')->write('mp_form_success', 1);
        }
        else {
            $this('session')->write('mp_form_response', $response);
            $this('session')->write('mp_form_notice', 1);
        }

        // send the visitor to the top of the form after submitting
        $anchor = $this->app->param('anchor', false);

        // fallback for xss attacks
        if (!$anchor || $anchor !== htmlentities(strip_tags($anchor))) {
            $anchor = $this->app->module('multiplane')->currentFormId;
        }

        $this->reroute($refererUrl.'#'.$anchor);

    }

}
