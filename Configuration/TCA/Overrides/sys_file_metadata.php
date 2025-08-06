<?php

defined('TYPO3') || die('Access denied.');

call_user_func(function ($_EXTKEY = 'video_vtt', $table = 'sys_file_metadata'): void {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns($table, [
        'tracks' => [
            'exclude' => true,
            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file.tracks',
            'displayCond' => 'USER:TRAW\\VideoVtt\\Resource\\DisplayCondition\\TextTrackDisplayCondition->displayTracksField',
            'config' => [
                'type' => 'file',
                'allowed' => 'vtt',
                'overrideChildTca' => [
                    'columns' => [
                        'link' => false,
                        'description' => false,
                        'alternative' => false,
                        'title' => false,
                    ],
                ],
            ],
        ],
        'poster' => [
            'exclude' => true,
            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_metadata.poster',
            'displayCond' => 'USER:TRAW\\VideoVtt\\Resource\\DisplayCondition\\PosterDisplayCondition->displayPoster',
            'config' => [
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
                'type' => 'file',
                'maxitems' => 1,
                'allowed' => 'common-image-types',
                'overrideChildTca' => [
                    'types' => [
                        \TYPO3\CMS\Core\Resource\FileType::IMAGE->value => [
                            'showitem' => '
                                    --palette--;;imageoverlayPalette,
                                    --palette--;;filePalette',
                        ],
                    ],
                    'columns' => [
                        'link' => false,
                        'description' => false,
                        'alternative' => false,
                        'title' => false,
                    ],
                ],
            ],
        ],
    ]);
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        $table,
        'tracks',
        (string)\TYPO3\CMS\Core\Resource\FileType::VIDEO->value,
        'after:duration'
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        $table,
        'poster',
        \TYPO3\CMS\Core\Resource\FileType::VIDEO->value,
        'after:duration'
    );
});
