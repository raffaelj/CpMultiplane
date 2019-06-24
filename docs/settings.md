# Adjust settings

Create a folder `config` in the root (`MP_DOCS_ROOT`) of your web project with a file `bootstrap.php`.

## Hard coded navigation

The inbuilt function `getNav()` looks at the database for all pages with a specified navigation type (currently only `main` and `footer`). If you want to skip this database request, add a hard coded navigation to `MP_DOCS_ROOT/config/bootstrap.php`.

```php
mp()->nav = [
    'main' => [
        [
        'title' => 'About Me',
        'permalink' => '/about-me',
        'active' => $app['route'] == '/about-me',
        ],
        [
        'title' => 'Contact',
        'permalink' => '/contact',
        'active' => $app['route'] == '/contact',
        ],
    ],
    'footer' => [
        [
        'title' => 'Imprint',
        'permalink' => '/imprint',
        'active' => $app['route'] == '/imprint',
        ],
    ],
];
```
