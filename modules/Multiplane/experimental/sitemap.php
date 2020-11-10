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
    $usePermalinks  = $this->module('multiplane')->usePermalinks;
    $defaultLang    = $this->module('multiplane')->defaultLang;
    $languages      = $this->module('multiplane')->getLanguages();
    $parentPage     = null;
    $route          = '';
    $pages          = $this->module('multiplane')->pages;
    $collections    = $this->module('multiplane')->sitemap;
    $structure      = $this->module('multiplane')->structure;

    $slugName       = $this->module('multiplane')->fieldNames['slug'];
    $permalinkName  = $this->module('multiplane')->fieldNames['permalink'];
    $publishedName  = $this->module('multiplane')->fieldNames['published'];
    $startpageName  = $this->module('multiplane')->fieldNames['startpage'];

    if (empty($collections)) {
        $collections = $this->module('multiplane')->use['collections'];
    }

    $options = [
        'filter' => [
            $publishedName => true,
        ],
        'sort' => [
            $startpageName => -1, // startpage on top
        ],
    ];

    // change sort order
    if (!$usePermalinks && $slugName != '_id') {
        $options['sort'][$slugName] = 1; // sort slugs alphabetically (by default lang)
    }
    if ($usePermalinks) {
        $options['sort'][$permalinkName] = 1; // sort permalinks alphabetically (by default lang)
    }

    foreach ($collections as $collection) {

        $_collection = $this->module('collections')->collection($collection);

        if (!$_collection) continue;

        $parentRoute = $structure[$_collection['name']]['slug'] ?? '';

        $hasLocalizedSlug = true;

        foreach ($this->module('collections')->find($collection, $options) as $page) {

            $isStartpage = !empty($page[$startpageName]);

            if (!$isMultilingual) {

                $_slugName = $usePermalinks ? $permalinkName : $slugName;

                if (empty($page[$_slugName]) && !$isStartpage) continue;

                $url = $siteUrl . $this->baseUrl($isStartpage ? '/' : $parentRoute . '/' . $page[$_slugName]);

                $url = \rtrim($url, '/');

                $xml->startElement('url');
                  $xml->startElement('loc');
                  $xml->text($url);
                  $xml->endElement();

                  $xml->startElement('lastmod');
                  $xml->text(\date('c', ($page['_modified']) ?? $page['_created']));
                  $xml->endElement();
                $xml->endElement();

            }

            else { // isMultilingual

                foreach ($languages as $lang) {

                    $_slugName = $usePermalinks ? $permalinkName : $slugName;

                    $langSuffix = $slugName != '_id' && $lang != $defaultLang ? '_'.$lang : '';

                    $parentRoute = $structure[$_collection['name']]['slug'.$langSuffix] ?? '';

                    if (empty($page[$_slugName.$langSuffix]) && !$isStartpage) continue;

                    if (!$usePermalinks) {
                        $url = $siteUrl. "/$lang" . $this->routeUrl($isStartpage ? '/' : $parentRoute . '/' . $page[$_slugName.$langSuffix]);
                    } else {
                        $url = $siteUrl.$this->routeUrl($isStartpage ? $lang : $page[$_slugName.$langSuffix]);
                    }

                    $url = \rtrim($url, '/');

                    $xml->startElement('url');

                        $xml->startElement('loc');
                        $xml->text($url);
                        $xml->endElement();

                        foreach ($languages as $l) {

                            if ($l == $lang) continue;

                            $lSuffix = $slugName != '_id' && $l != $defaultLang ? '_'.$l : '';

                            $_parentRoute = $structure[$_collection['name']]['slug'.$lSuffix] ?? '';

                            if (empty($page[$_slugName.$lSuffix]) && !$isStartpage) continue;

                            if (!$usePermalinks) {
                                $url = $siteUrl. "/$l" . $this->routeUrl($isStartpage ? '' : $_parentRoute . '/' . $page[$_slugName.$lSuffix]);
                            } else {
                                $url = $siteUrl. $this->routeUrl($isStartpage ? $l : $page[$_slugName.$lSuffix]);
                            }

                            $url = \rtrim($url, '/');

                            $xml->startElement('xhtml:link');
                            $xml->writeAttribute('rel', 'alternate');
                            $xml->writeAttribute('hreflang', $l);
                            $xml->writeAttribute('href', $url);
                            $xml->endElement();

                        }

                    $xml->endElement();

                }
            }

        }
    }

});
