<?php
return [
  'name' => 'basic',
  'label' => 'Basic',
  '_id' => 'basic',
  '_created' => 1586195272,
  '_modified' => 1593273612,
  'pages' => 'pages',
  'slugName' => 'slug',
  'theme' => 'rljbase',
  'use' => [
    'collections' => [
      0 => 'pages',
      1 => 'posts',
    ],
    'forms' => [
      0 => 'contact',
    ],
    'singletons' => [
      0 => 'site',
    ],
  ],
  'search' => [
    'enabled' => true,
  ],
  'guiDisplayCustomNav' => true,
  'formSendReferer' => true,
  'color' => '#A0D468',
  'siteSingleton' => 'site',
];
