<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Video VTT',
    'description' => 'Extend TYPO3 Video functionality',
    'category' => 'plugin',
    'author' => 'Thomas Rawiel',
    'author_email' => 'thomas.rawiel@gmail.com',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.5',
    'constraints' => [
        'depends' => [
            'typo3' => '10.0.0-12.99.99',
            'filemetadata' => '10.0.0-12.99.99'
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];