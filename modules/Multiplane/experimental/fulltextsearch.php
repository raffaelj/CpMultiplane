<?php

/**
 * fulltext search - experimental
 *
 * to do:
    * [x] special chars in wysiwyg (ä = &auml;...)
          --> install rljUtils addon and run cli `./cp fix-entity-encoding`
          https://github.com/raffaelj/cockpit_rljUtils/blob/master/cli/fix-entity-encoding.php
    * [ ] fix interference, if field names are 'url', 'collection' or 'weight'
    * [ ] search method AND - currently only OR
 *
 * config.yaml example for multiple collections in search results:

```yaml
multiplane:
    searchInCollections:
        pages:
            label: Pages
            weight: 10
            fields:
                - name: title
                  weight: 10
                - name: content
                  type: markdown
        posts:
            label: Blog
            weight: 5
            fields:
                - name: title
                  weight: 8
                - name: content
                  type: markdown
        calendar:
            label: Dates
            weight: 3
            fields:
                - name: title
                  weight: 8
                - name: content
                  type: wysiwyg
```

 *
 */

$this->on('multiplane.search', function($search, $list) {

    $isMultilingual = mp()->isMultilingual;
    $defaultLang    = mp()->defaultLang;
    $slugName       = mp()->slugName;
    $languages      = [];
    $lang           = $this('i18n')->locale;

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

    $options = [
        'filter' => ['published' => true],
        'lang' => $lang,
    ];

    foreach ($searchInCollections as $collection => &$c) {

        if (!is_array($c)) $c = [];

        $_collection = $this->module('collections')->collection($collection);

        if (!$_collection) continue;

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

            $route = $lang == $defaultLang ? 'route' : 'route_'.$lang;

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

        if ($isMultilingual && is_array($this['languages'])) {
            foreach ($this['languages'] as $l => $label) {
                if ($l == 'default') {
                    $languages[] = $defaultLang;
                } else {
                    $languages[] = $l;
                    $options['fields'][$slugName . '_' . $l] = true;
                }
            }
        }

        $options['filter']['$or'] = [];

        $suffix = $lang == $defaultLang ? '' : '_'.$lang;

        foreach ($c['fields'] as $field) {

            $options['fields'][$field['name']] = true;
            if ($lang != $defaultLang) $options['fields'][$field['name'].$suffix] = true;

            if (isset($field['type']) && $field['type'] == 'repeater') {

                // to do: cleanup/find cleaner solution
                $options['filter']['$or'][] = [$field['name'].$suffix => ['$fn' => 'repeaterSearch']];

            }

            elseif (isset($field['type']) && in_array($field['type'], ['wysiwyg', 'markdown'])) {

                // try to find only text inside html tags

                foreach ($searches as $search) {

                    // source: discussion in https://stackoverflow.com/a/39656464
                    // https://regex101.com/r/ZwXr4Y/4
                    $regex = "/(?<!&[^\s]){$search}(?![^<>]*(([\/\"']|]]|\b)>))/iu";

                    $options['filter']['$or'][] = [$field['name'].$suffix => ['$regex' => $regex]];
                }

            }

            else {
                foreach ($searches as $search) {
                    $options['filter']['$or'][] = [$field['name'].$suffix => ['$regex' => $search]];
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
                        return preg_replace('~('.implode('|', $searches).')~iu', '<mark>$1</mark>', $match[1], -1, $count) . $match[2] // highlight
                        . ($count && ($weight = $weight + $count * $increase) ? '' : ''); // increase weight
                    },
                    !empty($field['type'])
                        ? $this('fields')->{$field['type']}($entry[$name])
                        : (is_string($entry[$name]) ? $entry[$name] : '')
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


// helper functions - to do: cleanup/find cleaner solution
if (!function_exists('repeaterSearch')) {

    function repeaterSearch($field) {

        if (!$field || !is_array($field)) return false;

        $search = cockpit()->param('search', false);

        if (preg_match('/^(["\']).*\1$/m', $search)) {
            // exact match in quotes, still case insensitive
            $searches = [trim($search, '"\' \t\n\r\0\x0B')];
        }
        else {
            $searches = array_filter(explode(' ', $search), 'strlen');
        }

        $r = false;

        foreach ($searches as $b) {

            foreach ($field as $block) {

                if (\is_string($block['value'])) {
                    return (boolean) @\preg_match(isset($b[0]) && $b[0]=='/' ? $b : '/'.$b.'/iu', $block['value'], $match);
                }

                if ($block['field']['type'] == 'repeater' && \is_array($block['value'])) {
                    $r = repeaterSearch($block['value']);
                    if ($r) break;
                }
            }
            return $r;
        }
        return $r;
    }

}
