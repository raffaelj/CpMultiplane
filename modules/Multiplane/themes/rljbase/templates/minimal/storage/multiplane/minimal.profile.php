<?php
 return [
  'name' => 'minimal',
  'label' => 'Minimal',
  '_id' => 'minimal',
  '_created' => 1595175169,
  '_modified' => 1595175169,
  'search' => [
    'enabled' => false,
  ],
  'pages' => 'pages',
  'siteSingleton' => 'site',
  'slugName' => 'slug',
  'theme' => 'rljbase',
  'use' => [
    'collections' => [
      0 => 'pages',
    ],
    'singletons' => [
      0 => 'site',
    ],
  ],
  'description' => 'Minimal settings: One language, all pages and posts are in one collection',
  'pageTypeDetection' => 'type',
];
