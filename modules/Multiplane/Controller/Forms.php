<?php

namespace Multiplane\Controller;

class Forms extends \LimeExtra\Controller {

    public function index($params = []) {

        // stand-alone form page, e. g: example.com/form/form_name
        // or if multilingual example.com/en/form/form_name
        // useful as fallback for submit redirects if HTTP_REFERER is disabled

        if (!mp()->formStandalone) return false;

        if (!empty($params[':splat'][0])) {

            $slug = $params[':splat'][0];

            if (strpos($slug, '/')) {

                $parts = explode('/', $slug);

                if ($parts[0] == 'submit' && !empty($parts[1])) {
                    return $this->submit($parts[1]);
                }

            }

            $form = $params[':splat'][0];

            // init + load i18n
            $lang = $this('i18n')->locale;
            if ($translationspath = $this->path("mp_config:i18n/{$lang}.php")) {
                $this('i18n')->load($translationspath, $lang);
            }

            // load site data from site singleton
            $this->app->module('multiplane')->getSite();
            
            // add page to breadcrumbs
            $breadcrumbs = mp()->breadcrumbs;
            $breadcrumbs[] = ucfirst($form);
            mp()->breadcrumbs = $breadcrumbs;

            // hide from search engines
            $this->app->on('multiplane.seo', function(&$seo) use($form) {
                if (!isset($seo['robots'])) $seo['robots'] = [];
                $seo['robots'][] = 'noindex';
                $seo['robots'][] = 'nofollow';
                $seo['canonical'] = $this->baseUrl("/form/$form");
            });

            return $this->form($form);
        }

        return false;

    } // end of index()

    public function form($form = '', $options = []) {

        // submit get parameters:
        // 1: initial via form
        // 2: form has errors/notices
        // 3: success

        $sessionName = mp()->formSessionName;

        // lazy check for get param to avoid starting a session without user input
        $submit = isset($_GET['submit']) ? (int) $_GET['submit'] : false;
        if ($submit === 1 || $submit === 2) {
            $this('session')->init($sessionName);
        }

        $fields = mp()->getFormFields($form);

        if (!$fields) return false;

        $success = false;
        $notice  = false;

        // hide messages if session is expired and user calls the url again
        $expire = mp()->formSessionExpire;

        $call     = $this('session')->read("mp_form_call_$form", null);
        $response = $this('session')->read("mp_form_response_$form", null);

        if (!$call || ($call && (time() - $call > $expire))) {

            $this('session')->delete("mp_form_call_$form");
            $this('session')->delete("mp_form_response_$form");

        }

        $notice  =  $call && isset($_GET['submit']) && $_GET['submit'] == 2;
        $success = !$call && isset($_GET['submit']) && $_GET['submit'] == 3;

        $message = [
            'success' => $success ? mp()->formMessages['success'] : '',
            'notice'  => $notice  ? mp()->formMessages['notice']  : '',
            'error'   => mp()->formatErrorMessage($form),
        ];

        // if form is a standalone page
        $page = ['title' => ucfirst($form)];
        $site = mp()->site;

        return $this->render('views:partials/form.php', compact('page', 'form', 'fields', 'message', 'options'));

    } // end of form()

    public function submit($form = '') {

        $sessionName = mp()->formSessionName;
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

        $anchor = mp()->formIdPrefix.$form;

        $this->reroute($refererUrl.$submitQuery.'#'.$anchor);

    } // end of submit()

}
