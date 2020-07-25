<?php
 return array (
  'name' => 'posts',
  'label' => 'Posts',
  '_id' => 'posts',
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
      'name' => 'excerpt',
      'label' => 'Excerpt',
      'type' => 'wysiwyg',
      'default' => '',
      'info' => '',
      'group' => '',
      'localize' => true,
      'options' => 
      array (
        'editor' => 
        array (
          'format' => 'Basic',
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
    4 => 
    array (
      'name' => 'tags',
      'label' => 'Tags',
      'type' => 'tags',
      'default' => '',
      'info' => '',
      'group' => '',
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
      'name' => 'featured_image',
      'label' => 'Featured image',
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
    6 => 
    array (
      'name' => 'gallery',
      'label' => 'Image gallery',
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
    7 => 
    array (
      'name' => 'slug',
      'label' => '',
      'type' => 'text',
      'default' => '',
      'info' => 'Url part, e.g. https://example.com/this-is-the-slug-of-the-page',
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
    8 => 
    array (
      'name' => 'seo',
      'label' => 'SEO',
      'type' => 'seo',
      'default' => '',
      'info' => '',
      'group' => 'SEO',
      'localize' => false,
      'options' => 
      array (
        'fallback' => 
        array (
          'title' => 'title',
          'description' => 'excerpt',
          'image' => 'featured_image',
        ),
        'branding' => 'CpMultiplane',
      ),
      'width' => '1-1',
      'lst' => false,
      'acl' => 
      array (
      ),
    ),
  ),
  'sortable' => false,
  'in_menu' => false,
  '_created' => 1592640559,
  '_modified' => 1592728014,
  'color' => '#4FC1E9',
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
  'icon' => 'form-editor.svg',
  'multiplane' => 
  array (
    'sidebar' => true,
    'type' => 'subpages',
    'gui_in_header' => true,
  ),
);
