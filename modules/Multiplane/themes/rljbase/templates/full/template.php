<?php
// CpMultiplane full template config
return [

    'addons' => [
        'CpMultiplaneGUI' => 'https://github.com/raffaelj/cockpit_CpMultiplaneGUI/archive/master.zip',
        'FormValidation'  => 'https://github.com/raffaelj/cockpit_FormValidation/archive/master.zip',
        'ImageResize'     => 'https://github.com/raffaelj/cockpit_ImageResize/archive/master.zip',
        'rljUtils'        => 'https://github.com/raffaelj/cockpit_rljUtils/archive/master.zip',
        'UniqueSlugs'     => 'https://github.com/raffaelj/cockpit_UniqueSlugs/archive/master.zip',
        'VideoLinkField'  => 'https://github.com/raffaelj/cockpit_VideoLinkField/archive/master.zip',
        'EditorFormats'   => 'https://github.com/pauloamgomes/CockpitCms-EditorFormats/archive/master.zip',
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
