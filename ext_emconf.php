<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Video VTT',
    'description' => 'Extend TYPO3 Video functionality',
    'category' => 'plugin',
    'author' => 'Thomas Rawiel',
    'author_email' => 'thomas.rawiel@gmail.com',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '2.2.2',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-13.9.99',
            'filemetadata' => '12.4.0-13.9.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
