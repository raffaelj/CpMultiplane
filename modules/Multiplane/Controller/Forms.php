<?php

namespace Multiplane\Controller;

class Forms extends \LimeExtra\Controller {

    public function index($params = []) {

        // stand-alone form page, e. g: example.com/form/form_name
        // or if multilingual example.com/en/form/form_name
        // also useful as fallback for submit redirects if HTTP_REFERER is missing

        if (!$this->app->module('multiplane')->formStandalone) return false;

        if (!empty($params[':splat'][0])) {

            $slug = $params[':splat'][0];

            if (strpos($slug, '/')) {

                $parts = explode('/', $slug);

                if ($parts[0] == 'submit' && !empty($parts[1])) {
                    return $this->submit($parts[1]);
                }

            }

            $form = $params[':splat'][0];

            $formsInUse = $this->app->module('multiplane')->use['forms'] ?? [];
            if (!in_array($form, $formsInUse)) return false;

            $_form = $this->app->module('forms')->form($form);

            $formLang = $_form['multiplane']['language'] ?? '';
            if (!empty($formLang) && $formLang != $this->app->module('multiplane')->lang) {
                return false;
            }

            // add global viewvars
            $site = $this->app->module('multiplane')->getSite();
            $page = [
                'title' => $_form['label'] ?? ucfirst($form),
            ];
            $this->app->viewvars['page'] = $page;
            $this->app->viewvars['site'] = $site;

            // hide from search engines
            $this->app->on('multiplane.seo', function(&$seo) use($form) {
                if (!isset($seo['robots'])) $seo['robots'] = [];
                $seo['robots'][] = 'noindex';
                $seo['robots'][] = 'nofollow';
                $seo['canonical'] = $this->getSiteUrl() . $this->baseUrl("/form/$form");
            });

            $options = [
                'headline' => $page['title'],
            ];

            return $this->render('views:layouts/form.php', compact('form', 'options'));
        }

        return false;

    } // end of index()

    public function form($form = '', $options = []) {

        // submit get parameters:
        // 1: initial via form
        // 2: form has errors/notices
        // 3: success

        $sessionName = $this->app->module('multiplane')->formSessionName;

        // lazy check for get param to avoid starting a session without user input
        $submit = isset($_GET['submit']) ? (int) $_GET['submit'] : false;
        if ($submit === 1 || $submit === 2) {
            $this('session')->init($sessionName);
        }

        $fields = $this->app->module('multiplane')->getFormFields($form);

        if (!$fields) return false;

        $success = false;
        $notice  = false;

        // hide messages if session is expired and user calls the url again
        $expire = $this->app->module('multiplane')->formSessionExpire;

        $call     = $this('session')->read("mp_form_call_$form", null);
        $response = $this('session')->read("mp_form_response_$form", null);

        if (!$call || ($call && (time() - $call > $expire))) {

            $this('session')->delete("mp_form_call_$form");
            $this('session')->delete("mp_form_response_$form");

        }

        $notice  =  $call && isset($_GET['submit']) && $_GET['submit'] == 2;
        $success = !$call && isset($_GET['submit']) && $_GET['submit'] == 3;

        $_form = $this->app->module('forms')->form($form);
        $customFormMessages = isset($_form['formMessages']) && is_array($_form['formMessages']) ? $_form['formMessages'] : [];

        $formMessages = $this->app->module('multiplane')->formMessages;
        foreach ($customFormMessages as $k => $v) {
            if (is_string($v) && !empty(trim($v))) $formMessages[$k] = $v;
        }

        $message = [
            'success' => $success ? $formMessages['success'] : '',
            'notice'  => $notice  ? $formMessages['notice']  : '',
            'error'   => $this->app->module('multiplane')->formatErrorMessage($form),
            'error_generic' => $formMessages['error_generic'],
        ];

        return $this->render('views:partials/form.php', compact('form', 'fields', 'message', 'options', '_form'));

    } // end of form()

    public function submit($form = '') {

        $sessionName = $this->app->module('multiplane')->formSessionName;
        $submitQuery = '';

        $this('session')->init($sessionName);
        $this('session')->write("mp_form_call_$form", time());

        $referer = !empty($_SERVER['HTTP_REFERER']) ? parse_url(htmlspecialchars($_SERVER['HTTP_REFERER'])) : null;

        if (!$referer) {
            // might be disabled --> use a default fallback and link to single page form
            $path = $this->app->getSiteUrl() . $this->app->baseUrl('/form/'.$form);

            $referer = parse_url($path);
        }

        $refererUrl = @$referer['scheme'] . '://' .  @$referer['host'] . (isset($referer['port']) ? ":{$referer['port']}" : '') . @$referer['path'];

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

        foreach($_POST[$prefix.$form] as $key => $val) {

            if ($key == $formSubmitButtonName) continue;

            if (is_string($val)) {
                $postedData[$key] = htmlspecialchars(trim($val));
            }
            else {
                $postedData[$key] = $val;
            }
            // TODO: trim array values (multipleselect field)
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

            $this('session')->delete("mp_form_response_$form");

            // remove the session cookie
            if (\ini_get('session.use_cookies')) {
                $params = \session_get_cookie_params();
                \setcookie(
                    \session_name(),
                    '',
                    time() - 42000,
                    $params['path'], $params['domain'],
                    $params['secure'], $params['httponly']
                );
            }

            // destroy the session
            $this('session')->destroy();

            $submitQuery = '?submit=3';
        }
        else {
            $this('session')->write("mp_form_response_$form", $response);

            $submitQuery = '?submit=2';
        }

        $anchor = $this->app->module('multiplane')->formIdPrefix.$form;

        $this->reroute($refererUrl.$submitQuery.'#'.$anchor);

    } // end of submit()

}
