<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Video VTT',
    'description' => 'Extend TYPO3 Video functionality',
    'category' => 'plugin',
    'author' => 'Thomas Rawiel',
    'author_email' => 'thomas.rawiel@gmail.com',
    'state' => 'beta',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.5',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
            'filemetadata' => '12.4.0-12.4.99'
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];