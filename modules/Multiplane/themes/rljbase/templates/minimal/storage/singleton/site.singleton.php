<?php
 return array (
  'name' => 'site',
  'label' => 'Site',
  '_id' => 'site',
  'fields' => 
  array (
    0 => 
    array (
      'name' => 'site_name',
      'label' => 'Site name',
      'type' => 'text',
      'default' => '',
      'info' => 'This is the name of your website.',
      'group' => '',
      'localize' => true,
      'options' => 
      array (
      ),
      'width' => '1-3',
      'lst' => true,
      'acl' => 
      array (
      ),
      'required' => true,
    ),
    1 => 
    array (
      'name' => 'description',
      'label' => 'Short description',
      'type' => 'textarea',
      'default' => '',
      'info' => 'For search engines - max. 160 characters, fallback if a page has no description',
      'group' => '',
      'localize' => true,
      'options' => 
      array (
        'rows' => 3,
      ),
      'width' => '1-3',
      'lst' => true,
      'acl' => 
      array (
      ),
      'required' => false,
    ),
    2 => 
    array (
      'name' => 'logo',
      'label' => 'Logo',
      'type' => 'asset',
      'default' => '',
      'info' => '',
      'group' => '',
      'localize' => false,
      'options' => 
      array (
      ),
      'width' => '1-3',
      'lst' => true,
      'acl' => 
      array (
      ),
    ),
  ),
  'data' => NULL,
  '_created' => 1595164997,
  '_modified' => 1595174707,
  'description' => 'Minimal settings for CpMultiplane',
  'acl' => 
  array (
  ),
);
