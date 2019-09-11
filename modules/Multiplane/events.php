<?php

// fulltext search - experimental

// to do:
// * [ ] special chars in wysiwyg (ä = &auml;...)
// * [ ] fix interference, if field names are 'url', 'collection' or 'weight'
// * [ ] search method AND - currently only OR

$this->on('multiplane.search', function($search, $list) {

    $searchInCollections = mp()->searchInCollections;

    $pages = mp()->pages;
    $posts = mp()->posts;

    if (preg_match('/^(["\']).*\1$/m', $search)) {
        // exact match in quotes, still case insensitive
        $searches = [trim($search, '"\' \t\n\r\0\x0B')];
    }
    else {
        $searches = array_filter(explode(' ', $search), 'strlen');
    }

    // to do...
    // $multipleSearchTerms = count($searches) > 1 ? true : false;
    // $searchMethod        = $this->param('method', 'and');

    if (empty($searchInCollections)) {

        $searchInCollections = [
            'pages' => [
                'name' => mp()->pages,
                'route' => '',
                'weight' => 10,
                'fields' => [
                    [
                        'name' => 'title',
                        'weight' => 10,
                    ],
                    [
                        'name' => 'content',
                    ],
                ],
            ],
        ];

        if (!empty($posts)) {
            $searchInCollections[$posts] = [
                'name' => $posts,
                'route' => '/blog', // to do: dynamic detection
                'weight' => 5,
                'fields' => [
                    [
                        'name' => 'title',
                        'weight' => 8,
                    ],
                    [
                        'name' => 'content',
                    ],
                ],
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

        $options['fields'] = [
            $slugName => true,
        ];

        $options['filter']['$or'] = [];

        foreach ($c['fields'] as $field) {

            $options['fields'][$field['name']] = true;

            foreach ($searches as $search) {
                $options['filter']['$or'][] = [$field['name'] => ['$regex' => $search]];
            }

        }

        if ($lang != mp()->defaultLang) {

            $options['fields'] = [
                $slugName.'_'.$lang => true,
            ];

            foreach ($c['fields'] as $field) {

                $options['fields'][$field['name'].'_'.$lang] = true;

                foreach ($searches as $search) {
                    $options['filter']['$or'][] = [$field['name'].'_'.$lang => ['$regex' => $search]];
                }

            }

        }

        foreach ($this->module('collections')->find($collection, $options) as $entry) {

            $weight = !empty($c['weight']) ? $c['weight'] : 0;

            $item = [
                'url'        => $this->baseUrl(($c['route'] ?? '') . '/' . $entry[$slugName]),
                'collection' => !empty($c['label']) ? $c['label']
                                : (!empty($_collection['label'])
                                    ? $_collection['label']
                                    : $collection),
            ];

            foreach ($c['fields'] as $field) {

                $name = $field['name'];

                $increase = !empty($field['weight']) ? (int) $field['weight'] : 1;

                $item[$name] = preg_replace_callback(
                    '#((?:(?!<[/a-z]).)*)([^>]*>|$)#si',
                    function($match) use ($searches, &$weight, $increase) {
                        return preg_replace('~('.implode('|', $searches).')~i', '<mark>$1</mark>', $match[1], -1, $count) . $match[2] // highlight
                        . ($count && ($weight = $weight + $count * $increase) ? '' : ''); // increase weight
                    },
                    !empty($c['type'])
                        ? $this('fields')->{$c['type']}($entry[$name])
                        : $entry[$name]
                );

                // optional: rename keys to use the same/default theme template with different field names
                if (!empty($field['rename'])) {
                    $item[$field['rename']] = $item[$name];
                    unset($item[$name]);
                }

            }

            $item['weight'] = $weight;

            $list[] = $item;

        }

    }

});
