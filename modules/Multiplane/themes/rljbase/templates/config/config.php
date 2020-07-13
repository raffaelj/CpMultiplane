<?php
return [
    'app.name' => 'CpMultiplane',

    'i18n' => 'en',
    'languages' => [
        'default' => 'English',
        'de' => 'Deutsch',
    ],

    'unique_slugs' => [
        'collections' => [
            'pages' => 'title',
            'posts'  => 'title',
        ],
        'localize' => [
            'pages' => 'title',
            'posts'  => 'title',
        ],
    ],

    'multiplane' => [
        'profile' => 'default',
    ],
    
    'mailer' => [
        'from'       => getenv('MAILER_FROM'),
        'from_name'  => getenv('MAILER_FROM_NAME'),
        'transport'  => 'smtp',
        'host'       => getenv('MAILER_HOST'),
        'user'       => getenv('MAILER_USER'),
        'password'   => getenv('MAILER_PASSWORD'),
        'port'       => 587,
        'auth'       => true,
        'encryption' => 'starttls',
    ],

];
