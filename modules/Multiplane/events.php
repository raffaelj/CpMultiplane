<?php

// fulltext search - experimental

// to do:
// * [ ] special chars in wysiwyg (ä = &auml;...)
// * [ ] special chars case sensitivity - fixed via PR https://github.com/agentejo/cockpit/pull/1162

$this->on('multiplane.search', function($search, $list) {

    $searchInCollections = mp()->searchInCollections;

    $pages = mp()->pages;
    $posts = mp()->posts;

    if (empty($searchInCollections)) {

        $searchInCollections = [
            'pages' => [
                'name' => mp()->pages,
                'route' => '',
            ],
        ];

        if (!empty($posts)) {
            $searchInCollections[$posts] = [
                'name' => $posts,
                'route' => '/blog', // to do: dynamic detection
            ];
        }

    }

    $slugName = mp()->slugName;

    $lang = $this('i18n')->locale;

    $options = [
        'filter' => ['published' => true],
        'lang' => $lang,
    ];

    foreach ($searchInCollections as $collection => &$c) {

        if (!is_array($c)) $c = [];

        $_collection = $this->module('collections')->collection($collection);

        $title   = $c['title']   ?? 'title';
        $content = $c['content'] ?? 'content';

        // get field type for prerendering
        $type = '';
        foreach ($_collection['fields'] as $field) {
            if ($field['name'] == $content) {
                $type = $field['type'];
                break;
            }
        }

        // find route for sub pages
        if ($collection != $pages) {

            // to do: hard coded variant for all subpage modules
            $filter = [
                'published' => true,
                'subpagemodule.active' => true,
                'subpagemodule.collection' => $collection
            ];
            $projection = [
                '_id' => false,
                'subpagemodule' => true,
            ];

            $postRouteEntry = $this->module('collections')->findOne($pages, $filter, $projection, false, ['lang' => $lang]);

            $route = $lang == mp()->defaultLang ? 'route' : 'route_'.$lang;

            if (isset($postRouteEntry['subpagemodule'][$route])) {
                $c['route'] = $postRouteEntry['subpagemodule'][$route];
            }

        }

        $options = [
            'filter' => ['published' => true],
            'lang'   => $lang,
        ];

        if ($lang == mp()->defaultLang) {
            $options['filter']['$or'] = [
                [$title   => ['$regex' => $search]],
                [$content => ['$regex' => $search]],
            ];
            $options['fields'] = [
                $title    => true,
                $slugName => true,
                $content  => true,
            ];
        } else {
            $options['filter']['$or'] = [
                [$title.'_'.$lang   => ['$regex' => $search]],
                [$content.'_'.$lang => ['$regex' => $search]],
            ];
            $options['fields'] = [
                $title              => true,
                $title.'_'.$lang    => true,
                $slugName           => true,
                $slugName.'_'.$lang => true,
                $content            => true,
                $content.'_'.$lang  => true,
            ];
        }

        foreach ($this->module('collections')->find($collection, $options) as $entry) {

            $weight = 0;

            // inspired by: https://stackoverflow.com/a/48406963
            $highlightedContent = preg_replace_callback(
                '#((?:(?!<[/a-z]).)*)([^>]*>|$)#si',
                function($match) use ($search, &$weight) {
                    return preg_replace('~('.$search.')~i', '<mark>$1</mark>', $match[1], -1, $count) . $match[2] . ($count && $weight++ ? '' : '');
                },
                $this('fields')->{$type}($entry[$content])
            );
            $highlightedTitle = preg_replace_callback(
                '#((?:(?!<[/a-z]).)*)([^>]*>|$)#si',
                function($match) use ($search, &$weight) {
                    return preg_replace('~('.$search.')~i', '<mark>$1</mark>', $match[1], -1, $count) . $match[2] . ($count && $weight++ ? '' : '');
                },
                $this('fields')->{$type}($entry[$title])
            );

            $list[] = [
                'title'      => $highlightedTitle,
                'url'        => $this->baseUrl(($c['route'] ?? '') . '/' . $entry[$slugName]),
                'content'    => $highlightedContent,
                'weight'     => $weight,
                'collection' => !empty($_collection['label']) ? $_collection['label'] : $collection,
            ];

        }

    }

});
