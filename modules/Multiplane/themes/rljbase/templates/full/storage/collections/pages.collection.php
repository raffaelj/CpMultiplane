<?php
 return array (
  'name' => 'pages',
  'label' => 'Pages',
  '_id' => 'pages',
  'description' => 'Full',
  'fields' => 
  array (
    0 => 
    array (
      'name' => 'title',
      'label' => 'Title',
      'type' => 'text',
      'default' => '',
      'info' => '',
      'group' => '',
      'localize' => true,
      'options' => 
      array (
      ),
      'width' => '4-4',
      'lst' => true,
      'acl' => 
      array (
      ),
      'required' => true,
    ),
    1 => 
    array (
      'name' => 'published',
      'label' => 'Published',
      'type' => 'boolean',
      'default' => '',
      'info' => '',
      'group' => 'Config',
      'localize' => false,
      'options' => 
      array (
      ),
      'width' => '1-4',
      'lst' => true,
      'acl' => 
      array (
      ),
    ),
    2 => 
    array (
      'name' => 'content',
      'label' => 'Content',
      'type' => 'repeater',
      'default' => '',
      'info' => '',
      'localize' => true,
      'options' => 
      array (
        'fields' => 
        array (
          0 => 
          array (
            'type' => 'wysiwyg',
            'label' => 'Wysiwyg',
            'options' => 
            array (
              'editor' => 
              array (
                'format' => 'Advanced',
              ),
            ),
          ),
          1 => 
          array (
            'type' => 'asset',
            'label' => 'Asset',
          ),
          2 => 
          array (
            'type' => 'markdown',
            'label' => 'Markdown',
          ),
          3 => 
          array (
            'type' => 'videolink',
            'label' => 'Video',
          ),
          4 => 
          array (
            'type' => 'simple-gallery',
            'label' => 'Gallery',
          ),
          5 => 
          array (
            'type' => 'repeater',
            'label' => 'Repeater',
            'options' => 
            array (
              'fields' => 
              array (
                0 => 
                array (
                  'type' => 'wysiwyg',
                  'label' => 'Wysiwyg',
                  'options' => 
                  array (
                    'editor' => 
                    array (
                      'format' => 'Advanced',
                    ),
                  ),
                ),
                1 => 
                array (
                  'type' => 'asset',
                  'label' => 'Asset',
                ),
                2 => 
                array (
                  'type' => 'markdown',
                  'label' => 'Markdown',
                ),
                3 => 
                array (
                  'type' => 'videolink',
                  'label' => 'Video',
                ),
                4 => 
                array (
                  'type' => 'simple-gallery',
                  'label' => 'Gallery',
                ),
              ),
            ),
          ),
        ),
      ),
      'width' => '1-1',
      'lst' => false,
      'acl' => 
      array (
      ),
    ),
    3 => 
    array (
      'name' => 'featured_image',
      'label' => 'Seiten-Bild',
      'type' => 'asset',
      'default' => '',
      'info' => 'Will be displayed above the content',
      'group' => 'Media',
      'localize' => false,
      'options' => 
      array (
      ),
      'width' => '1-1',
      'lst' => true,
      'acl' => 
      array (
      ),
    ),
    4 => 
    array (
      'name' => 'gallery',
      'label' => 'Bildergalerie',
      'type' => 'simple-gallery',
      'default' => '',
      'info' => 'Will be displayed below the content',
      'group' => 'Media',
      'localize' => false,
      'options' => 
      array (
      ),
      'width' => '1-1',
      'lst' => false,
      'acl' => 
      array (
      ),
    ),
    5 => 
    array (
      'name' => 'slug',
      'label' => '',
      'type' => 'text',
      'default' => '',
      'info' => '',
      'group' => 'Config',
      'localize' => true,
      'options' => 
      array (
      ),
      'width' => '1-4',
      'lst' => false,
      'acl' => 
      array (
      ),
    ),
    6 => 
    array (
      'name' => 'seo',
      'label' => 'SEO',
      'type' => 'seo',
      'default' => '',
      'info' => '',
      'group' => 'SEO',
      'localize' => true,
      'options' => 
      array (
        'fallback' => 
        array (
          'title' => 'title',
          'description' => 'content',
          'image' => 'featured_image',
        ),
        'branding' => 'My great website',
      ),
      'width' => '1-1',
      'lst' => false,
      'acl' => 
      array (
      ),
    ),
  ),
  'sortable' => true,
  'in_menu' => false,
  '_created' => 1586194036,
  '_modified' => 1593293384,
  'color' => '#A0D468',
  'acl' => 
  array (
  ),
  'sort' => 
  array (
    'column' => '_created',
    'dir' => -1,
  ),
  'rules' => 
  array (
    'create' => 
    array (
      'enabled' => false,
    ),
    'read' => 
    array (
      'enabled' => false,
    ),
    'update' => 
    array (
      'enabled' => false,
    ),
    'delete' => 
    array (
      'enabled' => false,
    ),
  ),
  'icon' => 'adressbook.svg',
  'multiplane' => 
  array (
    'sidebar' => true,
    'type' => 'pages',
    'gui_in_header' => true,
  ),
);
