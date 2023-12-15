<?php

defined('TYPO3') or die('Access denied.');

call_user_func(function ($_EXTKEY = 'video_vtt', $table = 'sys_file_reference') {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns($table, [
        'loop' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.loop',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
            ],
        ],
        'mute' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.muted',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
            ],
        ],
        'showinfo' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.showinfo',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
            ],
        ],
        'controls' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.showcontrols',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
            ],
        ],
        'track_label' => [
            'displayCond' => [
                'AND' => [
                    'FIELD:fieldname:=:tracks',
                    'FIELD:tablenames:=:sys_file_metadata',
                ],
            ],
            'exclude' => true,
            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_label',
            'description' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_label.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
            ],
        ],
        'track_language' => [
            'displayCond' => [
                'AND' => [
                    'FIELD:fieldname:=:tracks',
                    'FIELD:tablenames:=:sys_file_metadata',
                ],
            ],
            'exclude' => true,
            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_language',
            'description' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_language.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 30,
            ],
        ],
        'track_type' => [
            'displayCond' => [
                'AND' => [
                    'FIELD:fieldname:=:tracks',
                    'FIELD:tablenames:=:sys_file_metadata',
                ],
            ],
            'exclude' => true,
            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_type',
            'description' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_type.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_type.captions',
                        'captions',
                    ],
                    [
                        'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_type.chapters',
                        'chapters',
                    ],
                    [
                        'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_type.descriptions',
                        'descriptions',
                    ],
                    [
                        'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_type.metadata',
                        'metadata',
                    ],
                    [
                        'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_type.subtitles',
                        'subtitles',
                    ],
                ],
                'default' => 'subtitles',
            ],
        ],
        'track_default' => [
            'displayCond' => [
                'AND' => [
                    'FIELD:fieldname:=:tracks',
                    'FIELD:tablenames:=:sys_file_metadata',
                ],
            ],
            'exclude' => true,
            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_default',
            'description' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_default.description',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
    ]);
    //changed order of fields
    $GLOBALS['TCA'][$table]['palettes']['videoOverlayPalette']['showitem']
        = 'title,description,
        --linebreak--,autoplay,mute,loop,showinfo,controls,';

    $GLOBALS['TCA'][$table]['palettes']['basicoverlayPalette']['showitem']
    = 'title,description,
    --linebreak--,track_default,--linebreak--,track_label,track_language,track_type';
});
