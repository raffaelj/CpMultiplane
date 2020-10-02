<?php

$this->helpers['seo'] = 'Multiplane\\Helper\\SEO';

$this->module('multiplane')->extend([

    /**
     * to do:
     * [ ] split this heavy function into smaller parts and move everything to
     *     `/Helper/SEO.php` class
     * [x] add spacer and site name to titles
     * [x] image
     * [x] schemas
     * [x] canonical
     * [ ] length check for description excerpt field fallback
     * [ ] description fallback to content snippet
     * [ ] htmlspecialchars ???
     * see themes/rljbase/views/partials/seometa.php
     */
    'getSeoMeta' => function($page = [], $withQueryString = false) {

        $site = $this->site;

        // some defaults

        $site_name = $site['site_name'] ?? $this->app['app.name'];
        $locale    = $this->lang;
        $site_url  = !$this->isMultilingual ? $this->app['site_url']
                     : $this->app['site_url'] . '/' . $locale;

        $url = $this->app['site_url'] . $this->app['route'];
        if ($withQueryString) {
            $url .= !empty($_SERVER['QUERY_STRING'])
                ? '?'.\urlencode($this->app->escape($_SERVER['QUERY_STRING'])) : '';
        }

        $seo = array_replace_recursive(
            isset($site['seo']) && is_array($site['seo']) ? $site['seo'] : [],
            isset($page['seo']) && is_array($page['seo']) ? $page['seo'] : []
        );

        $addBranding = $seo['config']['addBranding'] ?? true;
        $spacer   = !empty($seo['config']['spacer'])
                    ? $seo['config']['spacer'] : ' - ';
        $branding = !empty($seo['config']['branding'])
                    ? $seo['config']['branding'] : $site_name;

        $title = !empty($page['title']) ? \trim($page['title'])
                    . ($addBranding ? $spacer . $branding : '') : $site_name;

        $description =  !empty($page['description']) ? $page['description'] : (
                          !empty($page['excerpt']) ? \strip_tags($page['excerpt']) : (
                              $site['description'] ?? ''
                          )
                        );

        $images = !empty($page['seo']['image']) ? $page['seo']['image'] : (
                    !empty($page['featured_image']) ? $page['featured_image'] : (
                      !empty($site['seo']['image']) ? $site['seo']['image'] : (
                        !empty($site['logo']) ? $site['logo'] : []
                      )
                    )
                  );

        $default = [
            'title' => $title,
            'description' => $description,
            'og' => [],
            'twitter' => [],
            'robots' => [],
            'schemas' => [],
            'canonical' => '',
        ];
        foreach ($default as $k => $v) {
            if (empty($seo[$k])) $seo[$k] = $v;
        }

        // add default twitter meta

        $twitterDefault = [
            'card' => 'summary_large_image',
            'title' => $title,
            'description' => $description,
        ];
        foreach ($twitterDefault as $k => $v) {
            if (empty($seo['twitter'][$k])) $seo['twitter'][$k] = $v;
            elseif ($k == 'title' && $addBranding) $seo['twitter']['title'] .= $spacer . $branding;
        }
        if (empty($seo['twitter']['image'])) {
            $seo['twitter']['image'] = $this('seo')->imageUrl($images, false);
        }

        // add default og meta - more complicated, because og allows key duplicates

        $ogtype = !empty($page['type']) && in_array($page['type'], ['post', 'article']) ? 'article' : 'website';

        $ogDefault = [
            'title' => $title,
            'description' => $description,
            'locale' => $locale,
            'type' => $ogtype, // to do: "website" or "article"
            'site_name' => $site_name,
            'url' => $url,
            'image' => $this('seo')->imageUrl($images, true),
        ];

        if (empty($seo['og'])) {
            $seo['og'] = [];
            foreach ($ogDefault as $k => $v) $seo['og'][] = [$k => $v];
        }
        else {
            $currentOgKeys = [];
            $ogFromObj = [];
            foreach ($seo['og'] as $k => $item) {
                if (is_array($item)) {
                    $currentOgKeys[] = key($item);
                }
                else {
                    $ogFromObj[] = [$k => $item];
                    $currentOgKeys[] = $k;
                }
            }
            if (!empty($ogFromObj)) {
                $seo['og'] = $ogFromObj;
            }

            foreach ($ogDefault as $k => $v) {
                if (in_array($k, $currentOgKeys)) {
                    $key = array_search($k, $currentOgKeys);
                    if (empty($seo['og'][$key][$k])) $seo['og'][$key][$k] = $v;
                    elseif ($k == 'title' && $addBranding) $seo['og'][$key][$k] .= $spacer . $branding;
                } else {
                    $seo['og'][] = [$k => $v];
                }
            }
        }

        // add schemas

        if ($this->isStartpage) {

            $logo = !empty($site['logo']['_id']) ? $site['logo']['_id'] : false;
            $schema = [
                '@context' => 'https://schema.org',
                '@type' => 'Organization', // to do...
                'url' => $site_url,
                'name' => $site_name
            ];
            if ($logo) {
                $schema['logo'] = $this->app['site_url'].'/getImage?src='.$logo.'&w=1500&h=1500';
            }
            $seo['schemas'][] = $schema;

            if ($this->displaySearch) {

                $seo['schemas'][] = [
                    '@context' => 'https://schema.org',
                    '@type' => 'WebSite',
                    'url' => $site_url,
                    'name' => $site_name,
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => [
                            '@type' => 'EntryPoint',
                            'urlTemplate' => $site_url . '/search?search={search_term_string}',
                        ],
                        'query-input' => [
                            '@type' => 'PropertyValueSpecification',
                            'valueName' => 'search_term_string',
                            'valueRequired' => 'http://schema.org/True'
                        ]
                    ]
                ];
            }

        }
        else {

            $schema = [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => []
            ];

            $breadcrumbs = $this->breadcrumbs;
            $breadcrumbs[] = [
                'title' => $title,
                'slug'  => ''
            ];
            $c = count($breadcrumbs);

            $p = '';
            foreach ($breadcrumbs as $k => $v) {
                $p .= $v['slug'];
                $schema['itemListElement'][] = [
                    '@type' => 'ListItem',
                    'position' => $k + 1,
                    'item' => [
                        '@id' => $k == 0 ? $site_url : ( $k < $c - 1
                                 ? $site_url . $p
                                 : $url ),
                        'name' => $k == 0 ? $site_name : ( $k < $c - 1 ? $v['title'] : $title )
                    ]
                ];
            }
            $seo['schemas'][] = $schema;
        }

        $this->app->trigger('multiplane.seo', [&$seo]);

        return $seo;
    },

]);
