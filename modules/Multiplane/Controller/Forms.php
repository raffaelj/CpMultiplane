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

    } // end of index()

    public function form($form = '', $options = []) {

        $sessionName = mp()->formSessionName;

        $this('session')->init($sessionName);

        $fields = mp()->getFormFields($form);

        if (!$fields) return false;

        $page = []; // if form is a standalone page

        $success = false;
        $notice  = false;

        // hide messages if session is expired and user calls the url again
        $expire = mp()->formSessionExpire;

        $call = $this('session')->read("mp_form_call_$form", null);

        if (!$call || ($call && (time() - $call > $expire))) {

            $this('session')->delete("mp_form_call_$form");
            $this('session')->delete("mp_form_notice_$form");
            $this('session')->delete("mp_form_response_$form");
            $this('session')->delete("mp_form_success_$form");
            $this('session')->delete("mp_form_notice_$form");
            $this('session')->delete("mp_form_response_$form");

            $success = false;
            $notice  = false;

        }

        if ($this('session')->read("mp_form_notice_$form", false)) {
            $notice = true;
        }

        if ($this('session')->read("mp_form_success_$form", false)) {
            $success = true;
        }

        $message = [
            'success' => $success ? mp()->formMessages['success'] : '',
            'notice'  => $notice  ? mp()->formMessages['notice']  : '',
            'error'   => mp()->formatErrorMessage($form),
        ];

        $this('session')->delete("mp_form_notice_$form");
        $this('session')->delete("mp_form_success_$form");

        return $this->render('views:partials/form.php', compact('page', 'form', 'fields', 'message', 'options'));

    } // end of form()

    public function submit($form = '') {

        $sessionName = mp()->formSessionName;

        $this('session')->init($sessionName);
        $this('session')->write("mp_form_call_$form", time());

        $referer = !empty($_SERVER['HTTP_REFERER']) ? parse_url(htmlspecialchars($_SERVER['HTTP_REFERER'])) : null;

        if (!$referer) {
            // might be disabled, use a default fallback
            // to do...
            $path = $this->app['site_url'] . '/form/' . mp()->contact;
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
        $prefix = mp()->formIdPrefix;
        $formSubmitButtonName = mp()->formSubmitButtonName;

        $strlen = strlen($prefix);
        foreach($_POST[$form] as $key => $val) {
            if ($key == $formSubmitButtonName) continue;

            if (substr($key, 0, $strlen) == $prefix) {
                $k = substr($key, $strlen);
            } else {$k = $key;}

            $postedData[$k] = htmlspecialchars(trim($val));
        }
        
        if (mp()->formSendReferer) {
            $postedData['referer'] = $refererUrl;
        }

        // catch response stop from FormValidation addon
        try {
            $response = $this->module('forms')->submit($form, $postedData);
        } catch (\Exception $e) {
            $response = json_decode($e->getMessage(), true);
        }

        if (!isset($response['error'])) {
            $this('session')->delete("mp_form_response_$form");
            $this('session')->delete("mp_form_notice_$form");

            $this('session')->write("mp_form_success_$form", [$form => 1]);
        }
        else {
            $this('session')->write("mp_form_response_$form", $response);
            $this('session')->write("mp_form_notice_$form", 1);
        }

        $anchor = mp()->formIdPrefix.$form;

        $this->reroute($refererUrl.'#'.$anchor);

    } // end of submit()

}
