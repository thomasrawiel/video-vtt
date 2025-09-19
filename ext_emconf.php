<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Video VTT',
    'description' => 'Extend TYPO3 Video functionality',
    'category' => 'plugin',
    'author' => 'Thomas Rawiel',
    'author_email' => 'thomas.rawiel@gmail.com',
    'state' => 'stable',
    'version' => '2.5.1',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
            'filemetadata' => '13.4.0-13.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
