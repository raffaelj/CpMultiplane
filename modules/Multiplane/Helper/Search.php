<?php
/**
 * full-text search - experimental
 *
 * to do:
    * [x] special chars in wysiwyg (ä = &auml;...)
          --> install rljUtils addon and run cli `./cp fix-entity-encoding`
          https://github.com/raffaelj/cockpit_rljUtils/blob/master/cli/fix-entity-encoding.php
    * [ ] fix interference, if field names are 'url', 'collection' or 'weight'
    * [ ] search method AND - currently only OR
    * [ ] advanced search
    * [ ] pagination
    * [ ] snippet view
 *
 */

namespace Multiplane\Helper;

class Search extends \Lime\Helper {

    public $list;

    public function initialize() {

        $this->isMultilingual = $this->app->module('multiplane')->isMultilingual;
        $this->defaultLang    = $this->app->module('multiplane')->defaultLang;
        $this->languages      = $this->app->module('multiplane')->getLanguages();
        $this->lang           = $this->app->module('multiplane')->lang;
        $this->isDefaultLang  = $this->lang == $this->defaultLang;
        $this->langSuffix     = $this->app->module('multiplane')->getLanguageSuffix();

        $this->fieldNames     = $this->app->module('multiplane')->fieldNames;
        $this->minLength      = $this->app->module('multiplane')->get('search/minLength');
        $this->collections    = $this->app->module('multiplane')->get('search/collections');
        $this->searches       = [];
        $this->_search        = '';
        $this->fieldSearch    = [];
        $this->allowedFields  = ['title', 'content', 'tags', 'category'];
        $this->pages          = $this->app->module('multiplane')->pages;
        $this->usePermalinks  = $this->app->module('multiplane')->usePermalinks;
        $this->structure      = $this->app->module('multiplane')->get('structure');

        $this->list = new \ArrayObject([]);

    } // end of initialize()

    public function search($params = null) {

        $error = null;
        $count = null;

        $query = $this->app->param('search', false);

        if ($params) $query = $params;

        if ($query && ((\is_string($query) && \mb_strlen($query) >= $this->minLength)
            || \is_array($query))
            ) {

            $this->app->trigger('multiplane.search.before', [&$query, &$this->list]);

            $this->find($query);

            // custom sorting
            $sort = null;

            $this->app->trigger('multiplane.search.after', [&$query, &$this->list, &$sort]);

            if (!$sort || !\is_callable($sort)) {
                // sort by weight
                $sort = function($a, $b) {return $a['weight'] < $b['weight'] ? 1 : 0;};
            }

            $this->list->uasort($sort);

            $count = \count($this->list);

        }
        else {
            $error = 'Your search term must be at least '.$this->minLength.' characters long.';
        }

        $list = $this->list->getArrayCopy();

        return compact('list', 'query', 'error', 'count');

    } // end of search()

    public function find($query) {

        $this->parseQuery($query);

        if (empty($this->searches) && empty($this->fieldSearch)) return;

        if (empty($this->collections)) $this->config();

        foreach ($this->collections as $collection => &$c) {

            if (!\is_array($c)) $c = [];

            $_collection = $this->app->module('collections')->collection($collection);

            if (!$_collection) continue;

            if (!empty($this->structure[$collection]['_pid'])) {
                $parentSlugName = 'slug' . $this->langSuffix;
                $c['route'] = $this->structure[$collection][$parentSlugName];
            }

            $options = $this->generateFilterOptions($c);
            if (empty($options['filter'])) continue;
            $options['filter'][$this->fieldNames['published']] = true;

            foreach ($this->app->module('collections')->find($collection, $options) as $entry) {

                $this->list[] = $this->getWeightedItem($entry, $_collection, $c);

            }

        }

    } // end of find()

    public function parseQuery($query) {

        if (\is_string($query)) {
            if (\strpos($query, '{') === 0 && $arr = @\json_decode($query, true)) {
                $query = $arr;
            }
            else {
                $this->splitQuoteQuery($query);
            }
        }

        if (\is_array($query)) {
            $this->parseArrayQuery($query);
        }

    }

    public function splitQuoteQuery($search) {

        $_search = \trim($search);
        $this->_search = $_search;

        if (\preg_match('/^(["\']).*\1$/m', $_search)) {
            // exact match in quotes, still case insensitive
            $this->searches = [\preg_quote(\trim($_search, "\"' \t\n\r\0\x0B"), '/')];
        }
        else {
            $all = \array_filter(\explode(' ', $_search), 'strlen');
            $_search = \preg_quote($_search, '/');
            foreach ($all as $s) {
                if (\mb_strlen($s) > $this->minLength) { // skip single char words ("I", "a"...)
                    $this->searches[] = \preg_quote($s, '/');
                }
            }
        }

    }

    public function parseArrayQuery($search) {

        foreach ($search as $k => $v) {

            // numeric keys should be handled like a single string search with white spaces
            if (\is_numeric($k)) continue; // I'll fix that later

            // named keys should be handled as field searches
            if (!\in_array($k, $this->allowedFields)) continue;

            $this->fieldSearch[$k] = $v;

        }

    }

    public function config() {

        $collections = $this->app->module('multiplane')->use['collections'] ?? [];

        $defaultFields = ['title', 'content', 'tags']; // to do: should not be hardcoded

        foreach ($collections as $col) {

            $_collection = $this->app->module('collections')->collection($col);

            if (!$_collection) continue;

            $name     = $_collection['name'];
            $pageType = $_collection['multiplane']['type'] ?? 'pages';

            $types = [];
            $contentType = 'wysiwyg';
            foreach ($_collection['fields'] ?? [] as $field) {
                $types[$field['name']] = $field['type'];
            }

            $this->collections[$name] = [
                'name'   => $name,
                'route'  => $name == $this->pages ? '' : $this->app->module('multiplane')->getCollectionSlug($name),
                'weight' => $pageType == 'pages' ? 10 : 5,
            ];

            foreach ($defaultFields as $field) {
                if (isset($types[$field])) {
                    $this->collections[$name]['fields'][] = [
                        'name'   => $field,
                        'weight' => $pageType == 'pages' ? 10 : 8,
                        'type'   => $types[$field]
                    ];
                }
            }

            foreach ($this->fieldSearch as $k => $v) {
                if (!isset($types[$k])) continue;
                $this->collections[$name]['fields'][] = [
                    'name'   => $k,
                    'weight' => 10,
                    'type'   => $types[$k]
                ];
            }
        }

    } // end of config()

    public function generateFilterOptions($c) {

        $slugName      = $this->fieldNames['slug'];
        $permalinkName = $this->fieldNames['permalink'];
        $startpageName = $this->fieldNames['startpage'];

        $options = [
            'filter' => [],
            'lang'   => $this->lang,
        ];

        $options['fields'] = [
            $slugName      => true,
            $permalinkName => true,
            $startpageName => true,
            '_created'     => true,
        ];

        if ($this->isMultilingual) {
            foreach ($this->languages as $l) {
                if ($l != $this->defaultLang) {
                    $options['fields']["{$slugName}_{$l}"] = true;
                    $options['fields']["{$permalinkName}_{$l}"] = true;
                }
            }
        }

        if (!empty($this->searches)) {
            $options['filter']['$or'] = [];
        }

        $langSuffix = $this->langSuffix;
        $suffix = $langSuffix;

        foreach ($c['fields'] as $field) {

            $isFieldLocalized = $this->app->helper('mputils')->isFieldLocalized($field['name'], $c['name']);
            $suffix = $isFieldLocalized ? $langSuffix : '';

            $options['fields'][$field['name']] = true;

            if (!$this->isDefaultLang) {
                $options['fields'][$field['name'].$suffix] = true;
            }

            if (!empty($this->searches)) {

                if (isset($field['type']) && $field['type'] == 'repeater') {

                    $options['filter']['$or'][] = ['$where' => function($doc) use ($field, $suffix) {
                        return repeaterSearch($doc[$field['name'].$suffix]);
                    }];

                }

                elseif (isset($field['type']) && $field['type'] == 'layout') {

                    $options['filter']['$or'][] = ['$where' => function($doc) use ($field, $suffix) {
                        return layoutSearch($doc[$field['name'].$suffix]);
                    }];

                }

                elseif (isset($field['type']) && in_array($field['type'], ['wysiwyg', 'markdown'])) {

                    foreach ($this->searches as $search) {

                        // try to find only text inside html tags
                        // source: discussion in https://stackoverflow.com/a/39656464
                        // https://regex101.com/r/ZwXr4Y/4
                        $regex = "/(?<!&[^\s]){$search}(?![^<>]*(([\/\"']|]]|\b)>))/iu";

                        $options['filter']['$or'][] = [$field['name'].$suffix => ['$regex' => $regex]];
                    }

                }

                elseif (isset($field['type']) && $field['type'] == 'tags') {

                    foreach ($this->searches as $search) {

                        $options['filter']['$or'][] = [$field['name'].$suffix => ['$in' => [$search]]];

                    }

                }

                else {
                    foreach ($this->searches as $search) {
                        $options['filter']['$or'][] = [$field['name'].$suffix => ['$regex' => $search]];
                    }
                }

            }

            if (!empty($this->fieldSearch)) {

                if (isset($field['type']) && $field['type'] == 'tags') {

                    $tags = $this->fieldSearch[$field['name']];
                    if (!\is_array($tags)) $tags = [$tags];

                    if (!empty($tags)) {
                        $options['filter'][$field['name'].$suffix] = ['$in' => $tags];
                    }

                }

                // to do: other fields...

            }

        }

        return $options;

    } // end of generateFilterOptions()

    public function getWeightedItem($entry, $_collection, $c) {

        $slugName      = $this->fieldNames['slug'];
        $permalinkName = $this->fieldNames['permalink'];
        $startpageName = $this->fieldNames['startpage'];

        $collectionName = $_collection['name'];
        $collectionLabel = $collectionName;

        $weight = !empty($c['weight']) ? $c['weight'] : 0;

        $labelKey = 'label' . $this->langSuffix;
        if (!empty($this->structure[$collectionName][$labelKey])) {
            $collectionLabel = $this->structure[$collectionName][$labelKey];
        }

        $isStartpage = isset($entry[$startpageName]) && $entry[$startpageName] == true;

        $item = [
            '_id'        => $entry['_id'],
            '_created'   => $entry['_created'],
            'url'        => $this->app->baseUrl(($c['route'] ?? '') . '/' . ($isStartpage ? '' : $entry[$slugName])),
            'collection' => $collectionLabel,
        ];

        if ($this->usePermalinks) {
            $item['url'] = $entry[$permalinkName];
        }

        foreach ($c['fields'] as $field) {

            $name     = $field['name'];
            $increase = !empty($field['weight']) ? (int) $field['weight'] : 1;
            $display  = !isset($field['display']) ? true : $field['display'];
            $content  = !empty($field['type'])
                            && \in_array($field['type'], ['markdown', 'repeater', 'layout']) // to do: should not be hard coded
                            && \method_exists($this->app->helper('fields'), $field['type'])
                        ? $this->app->helper('fields')->{$field['type']}($entry[$name])
                        : $entry[$name];

            if (\is_string($content) && \count($this->searches) > 1) {
                // give it a weight boost, if the full expression of
                // multiple search terms was found
                $regex = "/(?<!&[^\s])".$this->_search."(?![^<>]*(([\/\"\']|]]|\b)>))/iu";

                \preg_match_all($regex, $content, $matches, PREG_SET_ORDER, 0);

                if ($count = \count($matches)) {
                    $weight += $count * $increase + 10;
                }
            }

            $regex = "/(?<!&[^\s])".\implode('|', $this->searches)."(?![^<>]*(([\/\"\']|]]|\b)>))/iu";

            if (\is_string($content)) {
                \preg_match_all($regex, $content, $matches, PREG_SET_ORDER, 0);

                $weight += \count($matches) * $increase;
            }

            if ($name == 'title' && !isset($item['_title']) && isset($entry['title'])) $item['_title'] = $entry['title'];

            if ($display) {

                if ($this->app->param('highlight', false) && !empty($this->searches) && \is_string($content)) {

                    $all = \count($this->searches) > 1
                        ? \array_merge([$this->_search], $this->searches) : $this->searches;

                    $regex = "/(?<!&[^\s])".\implode('|', $all)."(?![^<>]*(([\/\"\']|]]|\b)>))/iu";

                    $content = \preg_replace($regex, '<mark>$0</mark>', $content);

                }

                $item[$name] = $content;

            } else { $item[$name] = ''; }

            // optional: rename keys to use the same/default theme template with different field names
            if (!empty($field['rename'])) {
                $item[$field['rename']] = $item[$name];
                unset($item[$name]);
            }

        }

        $item['weight'] = $weight;

        return $item;

    } // end of getWeightedItem()

}


function repeaterSearch($field) {

    if (!$field || !\is_array($field)) return false;

    $searches = cockpit()->helper('search')->searches;

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
        if ($r) break;
    }
    return $r;
}

function layoutSearch($field) {

    if (!$field || !\is_array($field)) return false;

    $searches = cockpit()->helper('search')->searches;

    $r = false;

    foreach ($searches as $b) {

        $regex = "/(?<!&[^\s]){$b}(?![^<>]*(([\/\"']|]]|\b)>))/iu";

        foreach ($field as $block) {

            switch ($block['component']) {

                case 'text':
                case 'heading':
                case 'button':
                    $r = (boolean) @\preg_match($regex, $block['settings']['text'], $match);
                    break;

                case 'html':
                    $r = (boolean) @\preg_match($regex, $block['settings']['html'], $match);
                    break;

                case 'section':
                    $r = layoutSearch($block['children']);
                    break;

                case 'grid':
                    foreach ($block['columns'] as $col) {
                        $r = layoutSearch($col['children']);
                        if ($r) break;
                    }
                    break;

            }

            if ($r) break;

        }

        if ($r) break;

    }

    return $r;

}
