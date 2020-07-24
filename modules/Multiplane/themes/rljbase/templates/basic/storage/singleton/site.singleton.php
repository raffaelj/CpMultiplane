<?php
 return array (
  'name' => 'site',
  'label' => 'Site config',
  '_id' => 'site',
  'fields' => 
  array (
    0 => 
    array (
      'name' => 'site_name',
      'label' => 'Site name',
      'type' => 'text',
      'default' => '',
      'info' => '',
      'group' => '',
      'localize' => true,
      'options' => 
      array (
      ),
      'width' => '1-2',
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
      'info' => 'for search engines - max. 160 characters',
      'group' => '',
      'localize' => true,
      'options' => 
      array (
        'rows' => 3,
      ),
      'width' => '1-2',
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
      'width' => '1-2',
      'lst' => true,
      'acl' => 
      array (
      ),
    ),
    3 => 
    array (
      'name' => 'background_image',
      'label' => 'Background image',
      'type' => 'asset',
      'default' => '',
      'info' => '',
      'group' => '',
      'localize' => false,
      'options' => 
      array (
      ),
      'width' => '1-2',
      'lst' => true,
      'acl' => 
      array (
      ),
    ),
    4 => 
    array (
      'name' => 'seo',
      'label' => '',
      'type' => 'seo',
      'default' => '',
      'info' => '',
      'group' => '',
      'localize' => true,
      'options' => 
      array (
      ),
      'width' => '1-1',
      'lst' => true,
      'acl' => 
      array (
      ),
    ),
  ),
  'template' => '',
  'data' => NULL,
  '_created' => 1558967980,
  '_modified' => 1593348179,
  'description' => '',
  'acl' => 
  array (
    'author' => 
    array (
      'form' => true,
    ),
  ),
  'icon' => 'settings.svg',
  'color' => '#4FC1E9',
  'in_menu' => true,
  'gui_in_header' => true,
  'multiplane' => 
  array (
    'use' => true,
    'gui_in_header' => true,
  ),
);
