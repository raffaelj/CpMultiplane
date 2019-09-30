# Child theme demo of CpMultiplane

## config.yaml

```yaml
i18n: de
languages:
    default: Deutsch
    en: English

unique_slugs:
    collections:
        pages: title
        posts: title
    localize:
        pages: title
        posts: title

multiplane:
    theme: rljstripes
    slugName: slug
    isMultilingual: true
    searchInCollections:
        pages:
            label: Pages
            route: /
            weight: 10
            fields:
                - name: title
                  weight: 10
                - name: content
                  type: repeater
        posts:
            label: Blog
            route: /blog
            weight: 5
            fields:
                - name: title
                  weight: 8
                - name: content
                  type: repeater
```

## collection definitions

### pages

```php
<?php
 return array (
  'name' => 'pages',
  'label' => 'Pages',
  '_id' => 'pages5cfbbdd0a036a',
  'fields' => 
  array (
    0 => 
    array (
      'name' => 'title',
      'label' => 'Title',
      'type' => 'text',
      'default' => '',
      'info' => '',
      'localize' => true,
      'options' => 
      array (
      ),
      'width' => '3-4',
      'lst' => true,
      'required' => true,
    ),
    1 => 
    array (
      'name' => 'published',
      'label' => 'Published',
      'type' => 'boolean',
      'default' => '',
      'info' => '',
      'localize' => false,
      'options' => 
      array (
        'default' => false,
        'label' => false,
      ),
      'width' => '1-4',
      'lst' => true,
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
      'lst' => true,
    ),
    3 => 
    array (
      'name' => 'featured_image',
      'label' => 'Featured Image',
      'type' => 'asset',
      'default' => '',
      'info' => '',
      'localize' => false,
      'options' => 
      array (
      ),
      'width' => '1-3',
      'lst' => true,
    ),
    4 => 
    array (
      'name' => 'tags',
      'label' => 'Tags',
      'type' => 'tags',
      'default' => '',
      'info' => '',
      'localize' => false,
      'options' => 
      array (
      ),
      'width' => '1-3',
      'lst' => true,
    ),
    5 => 
    array (
      'name' => 'slug',
      'label' => '',
      'type' => 'text',
      'default' => '',
      'info' => '',
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
    ),
  ),
  'sortable' => true,
  'in_menu' => true,
  '_created' => 1560002000,
  '_modified' => 1569852882,
  'color' => '',
  'acl' => 
  array (
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
);
```

### posts

```
<?php
 return array (
  'name' => 'posts',
  'label' => 'Posts',
  '_id' => 'posts5cfbbde1e14ad',
  'fields' => 
  array (
    0 => 
    array (
      'name' => 'title',
      'label' => 'Title',
      'type' => 'text',
      'default' => '',
      'info' => '',
      'localize' => true,
      'options' => 
      array (
      ),
      'width' => '3-4',
      'lst' => true,
      'required' => true,
    ),
    1 => 
    array (
      'name' => 'published',
      'label' => 'Published',
      'type' => 'boolean',
      'default' => '',
      'info' => '',
      'localize' => false,
      'options' => 
      array (
        'default' => false,
        'label' => false,
      ),
      'width' => '1-4',
      'lst' => true,
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
      'lst' => true,
    ),
    3 => 
    array (
      'name' => 'excerpt',
      'label' => 'Excerpt',
      'type' => 'wysiwyg',
      'default' => '',
      'info' => '',
      'localize' => true,
      'options' => 
      array (
        'editor' => 
        array (
          'format' => 'Advanced',
        ),
      ),
      'width' => '1-2',
      'lst' => true,
    ),
    4 => 
    array (
      'name' => 'featured_image',
      'label' => 'Featured Image',
      'type' => 'asset',
      'default' => '',
      'info' => '',
      'localize' => false,
      'options' => 
      array (
      ),
      'width' => '1-2',
      'lst' => true,
    ),
    5 => 
    array (
      'name' => 'tags',
      'label' => 'Tags',
      'type' => 'tags',
      'default' => '',
      'info' => '',
      'localize' => false,
      'options' => 
      array (
      ),
      'width' => '1-2',
      'lst' => true,
    ),
    6 => 
    array (
      'name' => 'slug',
      'label' => '',
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
    ),
  ),
  'sortable' => false,
  'in_menu' => true,
  '_created' => 1560002017,
  '_modified' => 1569852845,
  'color' => '',
  'acl' => 
  array (
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
);
```
