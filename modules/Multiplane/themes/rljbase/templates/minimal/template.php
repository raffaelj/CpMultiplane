<?php
// CpMultiplane minimal template config

return [

    'addons' => [
        'CpMultiplaneGUI' => 'https://github.com/raffaelj/cockpit_CpMultiplaneGUI/archive/master.zip',
        'UniqueSlugs'     => 'https://github.com/raffaelj/cockpit_UniqueSlugs/archive/master.zip',
    ],

    'copy' => [
        [
            'source' => __DIR__.'/config',
            'destination' => '#config:',
        ],
        [
            'source' => __DIR__.'/storage',
            'destination' => '#storage:',
        ],
    ],

];
