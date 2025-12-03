<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Video VTT',
    'description' => 'Extend TYPO3 Video functionality',
    'category' => 'plugin',
    'author' => 'Thomas Rawiel',
    'author_email' => 'thomas.rawiel@gmail.com',
    'state' => 'stable',
    'version' => '2.7.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.20-14.4.99',
            'filemetadata' => '13.4.20-14.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
