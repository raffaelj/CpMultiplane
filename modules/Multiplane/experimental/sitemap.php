<?php

$this->on('multiplane.sitemap', function(&$xml) {

    $siteUrl        = $this['site_url'];
    $isMultilingual = mp()->isMultilingual;
    $defaultLang    = mp()->defaultLang;
    $slugName       = mp()->slugName;
    $languages      = [];
    $parentPage     = null;
    $route          = '';

    $collections = mp()->sitemap ?? [mp()->pages, mp()->posts];

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

    if ($isMultilingual && is_array($this['languages'])) {
        foreach ($this['languages'] as $lang => $label) {
            if ($lang == 'default') {
                $languages[] = $defaultLang;
            } else {
                $languages[] = $lang;
                $options['fields'][$slugName . '_' . $lang] = true;
            }
        }
    }

    foreach ($collections as $collection) {

        if (!$collection) continue;

        $hasLocalizedSlug = isset($this['unique_slugs']['localize'][$collection]);

        foreach($this->module('collections')->find($collection, $options) as $page) {

            $isStartpage = !empty($page['startpage']);

            if ($collection != mp()->pages) {

                $filter = [
                    'published' => true,
                    'subpagemodule.active' => true,
                    'subpagemodule.collection' => $collection,
                ];
                $projection = [
                    '_id' => false,
                    'subpagemodule' => true,
                ];

                $parentPage = $this->module('collections')->findOne(mp()->pages, $filter, $projection, false, null);

            }

            if (!$isMultilingual) {

                $route = '';
                if ($collection != mp()->pages
                    && !empty($parentPage['subpagemodule']['route'])) {
                    $route = '/' . ltrim($parentPage['subpagemodule']['route'], '/');
                }

                $xml->startElement('url');
                  $xml->startElement('loc');
                  $xml->text($siteUrl . $route . (!$isStartpage ? '/' . $page[mp()->slugName] : ''));
                  $xml->endElement();

                  $xml->startElement('lastmod');
                  $xml->text(date('c', ($page['_modified']) ?? $page['_created']));
                  $xml->endElement();
                $xml->endElement();

            }

            else {
                foreach ($languages as $lang) {

                    $suffix = $lang == $defaultLang ? '' : '_' . $lang;
                    $suffix2 = ($lang == $defaultLang) || !$hasLocalizedSlug  ? '' : '_' . $lang;
                    $route = '';

                    if ($collection != mp()->pages
                        && !empty($parentPage['subpagemodule']['route'.$suffix])) {
                        $route = '/' . ltrim($parentPage['subpagemodule']['route'.$suffix], '/');
                    }

                    $xml->startElement('url');

                      $xml->startElement('loc');
                      $xml->text($siteUrl . "/$lang" . $route . (!$isStartpage ? '/' . $page[$slugName.$suffix2] : ''));
                      $xml->endElement();

                      foreach ($languages as $l) {

                          if ($l == $lang) continue;
                          $suffix = $l == $defaultLang ? '' : '_' . $l;
                          $route = '';

                          if ($collection != mp()->pages
                              && !empty($parentPage['subpagemodule']['route'.$suffix])) {
                              $route = '/' . ltrim($parentPage['subpagemodule']['route'.$suffix], '/');
                          }

                          $suffix = ($l == $defaultLang) || !$hasLocalizedSlug ? '' : '_' . $l;

                          $xml->startElement('xhtml:link');
                          $xml->writeAttribute('rel', 'alternate');
                          $xml->writeAttribute('hreflang', $l);
                          $xml->writeAttribute('href', $siteUrl . "/$l" . $route . (!$isStartpage ? '/' . $page[$slugName . $suffix] : ''));
                          $xml->endElement();

                      }

                    $xml->endElement();

                }
            }

        }
    }

});
