<?php
return [
    // extend lexy parser for custom image resizing
    'lexy' => [
        'uploads' => 'raw',
        'logo' => [
            'width' => 200,
            'height' => 200,
            'quality' => 80,
        ],
        'thumbnail' => [
            'width' => 100,
            'height' => 100,
            'quality' => 70,
            'method' => 'thumbnail',
        ],
        'bigthumbnail' => [
            'width' => 200,
            'height' => 200,
            'quality' => 70,
            'method' => 'thumbnail',
        ],
        'image' => [
            'width' => 1000,
            'height' => 1000,
            'quality' => 80,
            'method' => 'bestFit',
        ],
        'headerimage' => [
            'width' => 968,
            'height' => 200,
            'quality' => 80,
            'method' => 'thumbnail',
        ],
    ],
];
