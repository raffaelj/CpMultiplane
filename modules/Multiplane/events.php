<?php



// fulltext search - experimental
$this->on('multiplane.search', function($search, $list) {

    $pages = mp()->pages;
    $posts = mp()->posts;

    $posts_route = '/blog';


    $collections = [$pages];
    if (!empty($posts)) $collections[] = $posts;
    // to do: other collections...

    $slugName = mp()->slugName;

    $lang = $this('i18n')->locale;

    $options = [
        'filter' => ['published' => true],
        'lang' => $lang,
    ];

    if ($lang == mp()->defaultLang) {
        $options['filter']['$or'] = [
            ['title' => ['$regex' => $search]],
            ['content' => ['$regex' => $search]],
        ];
        $options['fields'] = [
            'title' => true,
            $slugName => true,
            'content' => true,
        ];
    } else {
        $options['filter']['$or'] = [
            ['title_'.$lang => ['$regex' => $search]],
            ['content_'.$lang => ['$regex' => $search]],
        ];
        $options['fields'] = [
            'title' => true,
            'title_'.$lang => true,
            $slugName => true,
            $slugName.'_'.$lang => true,
            'content' => true,
            'content_'.$lang => true,
        ];
    }
    
    // find route to posts
    // to do: hard coded variant for all subpage modules
    $filter = [
        'published' => true,
        'subpagemodule.active' => true,
        'subpagemodule.collection' => $posts
    ];
    $projection = [
        '_id' => false,
        'subpagemodule' => true,
    ];

    $postRouteEntry = $this->module('collections')->findOne($pages, $filter, $projection, false, ['lang' => $lang]);
    $route = $lang == mp()->defaultLang ? 'route' : 'route_'.$lang;
    if (isset($postRouteEntry['subpagemodule'][$route])) {
        $posts_route = $postRouteEntry['subpagemodule'][$route];
    }

    foreach ($collections as $collection) {

        $_collection = $this->module('collections')->collection($collection);

        // get field type for prerendering
        $type = '';
        foreach ($_collection['fields'] as $field) {
            if ($field['name'] == 'content') {
                $type = $field['type'];
                break;
            }
        }

        foreach ($this->module('collections')->find($collection, $options) as $entry) {

            $weight = 0;

            // inspired by: https://stackoverflow.com/a/48406963
            $highlightedContent = preg_replace_callback(
                '#((?:(?!<[/a-z]).)*)([^>]*>|$)#si',
                function($match) use ($search, &$weight) {
                    return preg_replace('~('.$search.')~i', '<mark>$1</mark>', $match[1], -1, $count) . $match[2] . ($count && $weight++ ? '' : '');
                },
                $this('fields')->{$type}($entry['content'])
            );

            $list[] = [
                'title' => $entry['title'],
                'url' => $this->baseUrl(($collection == $posts ? $posts_route : '') . '/' . $entry[$slugName]),
                'content' => $highlightedContent,
                'weight' => $weight,
            ];

        }

    }

});
