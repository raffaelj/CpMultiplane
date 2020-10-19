<?php
return [
    'app.name' => 'mpfull',

    'i18n' => 'en',
    'languages' => [
        'default' => 'English',
        'de'      => 'Deutsch',
        'fr'      => 'France',
    ],

    'multiplane' => [
        'profile' => 'full',
    ],

    'groups' => [
        
    ],

    'mailer' => [
        'from'       => getenv('MAILER_FROM'),
        'from_name'  => getenv('MAILER_FROM_NAME')  ?? 'Web form',
        'transport'  => getenv('MAILER_TRANSPORT')  ?? 'smtp',
        'host'       => getenv('MAILER_HOST'),
        'user'       => getenv('MAILER_USER'),
        'password'   => getenv('MAILER_PASSWORD'),
        'port'       => getenv('MAILER_PORT')       ?? 587,
        'auth'       => getenv('MAILER_AUTH')       ?? true,
        'encryption' => getenv('MAILER_ENCRYPTION') ?? 'starttls',
    ],

];
