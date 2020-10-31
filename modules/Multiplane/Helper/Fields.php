<?php

namespace Multiplane\Helper;

class Fields extends \Lime\Helper {

    public function index($content = null, $options = []) {

        if (is_string($content)) {
            return $content;
        }

        if (is_array($content)) {
            return json_encode($content);
        }

        return '';

    }

    // public function __call($name, $arguments) {

        // if (is_callable([$this, $name]) && method_exists($this, $name)) {

            // return call_user_func_array([$this, $name], $arguments);
        // }

        // return call_user_func_array([$this, 'index'], $arguments);

    // }

    public function render($template, $slots = []) {

        $olayout = $this->app->layout;
        $this->app->layout = false;

        $output = $this->app->view($template, $slots);

        $this->app->layout = $olayout;

        return $output;

    }

    public function text($content = '', $options = []) {

        if ($fieldTemplate = $this->app->path('views:fields/text.php')) {

            return $this->render($fieldTemplate, compact('content', 'options'));

        }

        return '<p>' . $content . '</p>';

    }

    public function textarea($content = '', $options = []) {

        if ($fieldTemplate = $this->app->path('views:fields/textarea.php')) {

            return $this->render($fieldTemplate, compact('content', 'options'));

        }

        return '<p>' . nl2br($content) . '</p>';

    }

    public function wysiwyg($content = '', $options = []) {

        if ($fieldTemplate = $this->app->path('views:fields/wysiwyg.php')) {

            return $this->render($fieldTemplate, compact('content', 'options'));

        }

        return $content;

    }

    public function markdown($content = '', $options = [], $extra = false) {

        if ($fieldTemplate = $this->app->path('views:fields/markdown.php')) {

            return $this->render($fieldTemplate, compact('content', 'options', 'extra'));

        }

        return $this->app->module('cockpit')->markdown($content, $extra);

    }
/* 
    public function set($content = null, $options = []) {

        // to do...
        return $this->index($content);

    }
 */
    public function repeater($content = null, $options = []) {

        if (!$content || !is_array($content)) return '';

        if ($fieldTemplate = $this->app->path('views:fields/repeater.php')) {

            return $this->render($fieldTemplate, compact('content', 'options'));

        }

        $out = '';

        foreach ($content as $i => $block) {

            $cmd = $block['field']['type'];

            if (is_callable([$this, $cmd]) && method_exists($this, $cmd)) {

                $out .= $this->{$cmd}($block['value']);

            }

        }

        return $out;

    }

    public function layout($content = null) {

        if (!$content || !is_array($content)) return '';

        if ($fieldTemplate = $this->app->path('views:fields/layout.php')) {

            return $this->render($fieldTemplate, compact('content'));

        }

        $out = '';

        return $out;

    }

    // deprecated
    public function replaceRelativeLinksInHTML($html) {

        $isMultilingual = $this->app->module('multiplane')->isMultilingual;

        $isInSubFolder = !empty(MP_BASE_URL);

        if (!$isMultilingual && !$isInSubFolder) {
            return $html;
        }

        $dom = new \DomDocument();

        // inspired by https://stackoverflow.com/a/45680712 and
        // https://stackoverflow.com/questions/4879946/how-to-savehtml-of-domdocument-without-html-wrapper#comment86181089_45680712

        // disable errors - workaround for HTML5 tags like "nav" or "header", https://stackoverflow.com/a/6090728
        libxml_use_internal_errors(true);

        $dom->loadHTML('<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>' . $html . '</body></html>');

        libxml_clear_errors();

        $anchors = $dom->getElementsByTagName('a');

        foreach ($anchors as $a) {

            $href = $a->getAttribute('href');

            if (strpos($href, '/') === 0 && strpos($href, '//') === false) {

                // to do: compare with language prefix

                $a->setAttribute('href', $this->app->baseUrl($href));

            }

        }

        // fix image sources inside wysiwyg field when switching between remote
        // and sub folder production setup
        $images = $dom->getElementsByTagName('img');

        foreach ($images as $img) {

            $src = $img->getAttribute('src');

            if (strpos($src, '/getImage') === 0 && strpos($src, '//') === false) {

                $img->setAttribute('src', $this->app->routeUrl($src));

            }

        }

        return substr(trim($dom->saveHTML()), 199, -14);

    }

}
