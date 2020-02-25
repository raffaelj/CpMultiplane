<?php

/**
 * Generate example.com/sitemap.xml
 *
 * config.yaml example for multiple collections in sitemap:

```yaml
multiplane:
    sitemap: ['pages', 'posts', 'calendar'] # array of collection names
```

 *
 */

$this->on('multiplane.sitemap', function(&$xml) {

    $siteUrl        = $this['site_url'];
    $isMultilingual = $this->module('multiplane')->isMultilingual;
    $defaultLang    = $this->module('multiplane')->defaultLang;
    $slugName       = $this->module('multiplane')->slugName;
    $languages      = $this->module('multiplane')->getLanguages();
    $parentPage     = null;
    $route          = '';
    $pages          = $this->module('multiplane')->pages;
    $collections    = $this->module('multiplane')->sitemap;

    if (empty($collections)) {
        $collections = $this->module('multiplane')->use['collections'];
    }

    $options = [
        'filter' => [
            'published' => true,
        ],
        'fields' => [
            $slugName => true,
            '_modified' => true,
            'startpage' => true,
        ],
    ];

    if ($isMultilingual && $slugName != '_id') {
        foreach ($languages as $lang) {
            if ($lang != $defaultLang) {
                $options['fields']["{$slugName}_{$lang}"] = true;
            }
        }
    }

    foreach ($collections as $collection) {

        $_collection = $this->module('collections')->collection($collection);
        if (!$_collection) continue;

        $hasLocalizedSlug = isset($this['unique_slugs']['localize'][$collection]);

        foreach($this->module('collections')->find($collection, $options) as $page) {

            $isStartpage = !empty($page['startpage']);

            if ($collection != $pages) {

                $filter = [
                    'published' => true,
                    'subpagemodule.active' => true,
                    'subpagemodule.collection' => $collection,
                ];
                $projection = [
                    '_id' => false,
                    'subpagemodule' => true,
                ];

                $parentPage = $this->module('collections')->findOne($pages, $filter, $projection, false, null);

            }

            if (!$isMultilingual) {

                $route = '';
                if ($collection != $pages
                    && !empty($parentPage['subpagemodule']['route'])) {
                    $route = '/' . ltrim($parentPage['subpagemodule']['route'], '/');
                }

                if (empty($page[$slugName])) continue;

                $xml->startElement('url');
                  $xml->startElement('loc');
                  $xml->text($siteUrl . $route . ($isStartpage ? '' : '/' . $page[$slugName]));
                  $xml->endElement();

                  $xml->startElement('lastmod');
                  $xml->text(date('c', ($page['_modified']) ?? $page['_created']));
                  $xml->endElement();
                $xml->endElement();

            }

            else {
                foreach ($languages as $lang) {

                    $route      = '';
                    $slugSuffix = ($lang == $defaultLang) || !$hasLocalizedSlug
                                  || $slugName == '_id' ? '' : '_' . $lang;

                    $suffix = $lang == $defaultLang ? '' : '_' . $lang;
                    if ($collection != $pages
                        && !empty($parentPage['subpagemodule']['route'.$suffix])) {
                        $route = '/' . ltrim($parentPage['subpagemodule']['route'.$suffix], '/');
                    }

                    if (!empty($page[$slugName.$slugSuffix]) || $isStartpage) {

                      $xml->startElement('url');

                        $xml->startElement('loc');
                        $xml->text($siteUrl . "/$lang" . $route . ($isStartpage ? '' : '/' . $page[$slugName.$slugSuffix]));
                        $xml->endElement();

                        foreach ($languages as $l) {

                            if ($l == $lang) continue;
                            $suffix = $l == $defaultLang ? '' : '_' . $l;
                            $route = '';

                            if ($collection != $pages
                                && !empty($parentPage['subpagemodule']['route'.$suffix])) {
                                $route = '/' . ltrim($parentPage['subpagemodule']['route'.$suffix], '/');
                            }

                            $suffix = ($l == $defaultLang) || !$hasLocalizedSlug
                                      || $slugName == '_id' ? '' : '_' . $l;

                            if (empty($page[$slugName . $suffix])) continue;
                            
                            $xml->startElement('xhtml:link');
                            $xml->writeAttribute('rel', 'alternate');
                            $xml->writeAttribute('hreflang', $l);
                            $xml->writeAttribute('href', $siteUrl . "/$l" . $route . ($isStartpage ? '' : '/' . $page[$slugName . $suffix]));
                            $xml->endElement();

                        }

                      $xml->endElement();

                    }
                }
            }

        }
    }

});
