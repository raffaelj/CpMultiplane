<?php

// The following methods are used inside template files, so I moved them away from bootstrap.php

$this->module('multiplane')->extend([

    'getNav' => function($collection = null, $type = '') {

        // if hard coded nav is present, return this one
        if (isset($this->nav[$type])) return $this->nav[$type];

        if (!$collection) $collection = $this->pages;

        $collection = $this->app->module('collections')->collection($collection);

        if (!$collection) return [];

        $isSortable = $collection['sortable'] ?? false;

        $slugName      = $this->fieldNames['slug'];
        $navName       = $this->fieldNames['nav'];
        $titleName     = $this->fieldNames['title'];
        $startpageName = $this->fieldNames['startpage'];
        $publishedName = $this->fieldNames['published'];
        $permalinkName = $this->fieldNames['permalink'];

        $options = [
            'filter' => [
                $publishedName => true,
            ],
            'fields' => [
                $slugName      => true,
                $titleName     => true,
                $navName       => true,
                '_pid'         => true,
                '_o'           => true,
                $startpageName => true,
                $permalinkName => true,
            ],
        ];

        if (!empty($type)) {
            $options['filter'][$navName] = ['$has' => $type];
        } else {
            $options['filter'][$navName] = ['$size' => ['$gt' => 0]];
        }

        if ($this->isMultilingual) {

            $lang = $this->lang;

            $options['lang'] = $lang;

            if ($lang != $this->defaultLang) {
                $options['fields']["{$titleName}_{$lang}"] = true;
                $options['fields']["{$permalinkName}_{$lang}"] = true;
                if ($slugName != '_id') {
                    $options['fields']["{$slugName}_{$lang}"] = true;
                }
            }

        }

        $entries = $this->app->module('collections')->find($collection['name'], $options);

        if (!$entries) return false;

        foreach ($entries as &$n) {

            $active = false;
            if ($this->hasParentPage && $n[$slugName] == $this->parentPage[$slugName]) {
                $active = true;
            } elseif($this->currentSlug == $n[$slugName]
                || ($this->currentSlug == '' && !empty($n[$startpageName]))
                ) {
                $active = true;
            }

            $n['active'] = $active;

            if ($this->usePermalinks) {
                $n['url'] = $n[$permalinkName];
                if ($slugName != '_id') unset($n[$slugName]);
            }

        }

        if ($isSortable) {

            $entries = $this->app->helper('utils')->buildTree($entries, [
                'parent_id_column_name' => '_pid',
                'children_key_name'     => 'children',
                'id_column_name'        => '_id',
                'sort_column_name'      => '_o'
            ]);

        }

        return $entries;

    }, // end of getNav()

    'getLanguageSwitch' => function($id = '') {

        $languages = $this->getLanguages(true);

        $slugName      = $this->fieldNames['slug'];
        $publishedName = $this->fieldNames['published'];
        $permalinkName = $this->fieldNames['permalink'];
        $contentName   = $this->fieldNames['content'];

        foreach ($languages as &$l) {

            $lang = $l['code'];
            $slug = '';

            $langSuffix = $lang != $this->defaultLang && $slugName != '_id' ? '_'.$lang : '';

            if ($this->isStartpage || empty($id)) {
                $l['url'] = $this->app->routeUrl("/{$lang}");
                continue;
            }

            else {
                $filter = [
                    $publishedName => true,
                    '_id'          => $id,
                ];
                $projection = [
                    $contentName => false,
                ];
                $entry = $this->app->module('collections')->findOne($this->collection, $filter, $projection, false, ['lang' => $lang]);

                if ($this->usePermalinks) {
                    $l['url'] = $this->app->routeUrl($entry[$permalinkName]);
                } else {

                    if (!isset($entry[$slugName])) continue;

                    $slug = $entry[$slugName];
                    if ($this->parentPage) {
                        $route = $this->parentPage[$slugName.$langSuffix];
                        $l['url'] = $this->app->routeUrl('/'.$lang.'/'.$route.'/'.$entry[$slugName]);
                    } else {
                        $l['url'] = $this->app->routeUrl('/'.$lang.'/'.$entry[$slugName]);
                    }
                }
            }

        }

        return $languages;

    }, // end of getLanguageSwitch()

    'getRouteToPrivacyPage' => function() {

        $filter = [
            $this->fieldNames['published']   => true,
            $this->fieldNames['privacypage'] => true
        ];

        $lang = $this->lang;

        $slugName = $this->fieldNames['slug'];

        $projection = [
            $slugName => true,
            '_id'     => false,
        ];
        if ($this->isMultilingual && $lang != $this->defaultLang) {
            $projection[$slugName.'_'.$lang] = true;
        }

        $page = $this->app->module('collections')->findOne($this->pages, $filter, $projection, null, false, ['lang' => $lang]);

        $route = $page[$slugName.'_'.$lang] ?? $page[$slugName] ?? '';

        return '/'.$route;

    }, // end of getRouteToPrivacyPage()

    // same as Lime\App->assets(), but with a switch to different script function
    // temporary fix to avoid nu validator warning
    'assets' => function($src, $version=false){

        $list = [];

        foreach ((array)$src as $asset) {

            $src = $asset;

            if (\is_array($asset)) {
                \extract($asset);
            }

            if (@\substr($src, -3) == '.js') {
                $list[] = $this->script($asset, $version);
            }

            if (@\substr($src, -4) == '.css') {
                $list[] = $this->app->style($asset, $version);
            }
        }

        return \implode("\n", $list);

    }, // end of assets()

    // same as Lime\App->script(), but without `type=javascript`
    // temporary fix to avoid nu validator warning
    'script' => function ($src, $version=false){

        $list = [];

        foreach ((array)$src as $script) {

            $src  = $script;

            if (\is_array($script)) {
                \extract($script);
            }

            $ispath = \strpos($src, ':') !== false && !\preg_match('#^(|http\:|https\:)//#', $src);
            $list[] = '<script src="'.($ispath ? $this->app->pathToUrl($src):$src).($version ? "?ver={$version}":"").'"></script>';
        }

        return \implode("\n", $list);

    }, // end of script()

    'userStyles' => function() {

        if (empty($this->styles)) return;

        echo "\r\n<style>\r\n";

        foreach ($this->styles as $selector => $style) {
            if (\is_numeric($selector) && is_string($style)) {
                echo $style . "\r\n";
                continue;
            }
            elseif (\is_string($style)) {
                echo "$selector $style" . "\r\n";
            }
        }

        echo "</style>\r\n";

    }, // end of userStyles()

    'userScripts' => function() {

        if (empty($this->scripts)) return;

        echo "\r\n<script>\r\n";

        foreach ($this->scripts as $script) {
            echo $script . "\r\n";
        }

        echo "</script>\r\n";

    }, // end of userScripts()

    'baseUrl' => function($url) {
        $baseUrl = $this->app->baseUrl($url);
        return $baseUrl == '/' ? $baseUrl : rtrim($baseUrl, '/');
    },

    'base' => function($url) {
        echo $this->baseUrl($url);
    }

]);
