<?php

defined('TYPO3') || die('Access denied.');

call_user_func(function ($_EXTKEY = 'video_vtt', $table = 'sys_file_reference'): void {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns($table, [
        'loop' => [
            'exclude' => true,
            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.loop',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
            ],
        ],
        'mute' => [
            'exclude' => true,
            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.muted',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
            ],
        ],
        'showinfo' => [
            'exclude' => true,
            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.showinfo',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
            ],
        ],
        'controls' => [
            'exclude' => true,
            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.showcontrols',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 1,
            ],
        ],
        'track_label' => [
            'exclude' => true,
            'displayCond' => [
                'AND' => [
                    'FIELD:fieldname:=:tracks',
                    'FIELD:tablenames:=:sys_file_metadata',
                ],
            ],
            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_label',
            'description' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_label.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
            ],
        ],
        'track_language' => [
            'exclude' => true,
            'displayCond' => [
                'AND' => [
                    'FIELD:fieldname:=:tracks',
                    'FIELD:tablenames:=:sys_file_metadata',
                ],
            ],
            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_language',
            'description' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_language.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 30,
            ],
        ],
        'track_type' => [
            'exclude' => true,
            'displayCond' => [
                'AND' => [
                    'FIELD:fieldname:=:tracks',
                    'FIELD:tablenames:=:sys_file_metadata',
                ],
            ],

            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_type',
            'description' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_type.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_type.captions',
                        'value' => 'captions',
                    ],
                    [
                        'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_type.chapters',
                        'value' => 'chapters',
                    ],
                    [
                        'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_type.descriptions',
                        'value' => 'descriptions',
                    ],
                    [
                        'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_type.metadata',
                        'value' => 'metadata',
                    ],
                    [
                        'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_type.subtitles',
                        'value' => 'subtitles',
                    ],
                ],
                'default' => 'subtitles',
            ],
        ],
        'track_default' => [
            'exclude' => true,
            'displayCond' => [
                'AND' => [
                    'FIELD:fieldname:=:tracks',
                    'FIELD:tablenames:=:sys_file_metadata',
                ],
            ],
            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_default',
            'description' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.track_default.description',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
        'controlslist' => [
            'exclude' => true,
            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.controlsList',
            'displayCond' => 'FIELD:controls:REQ:TRUE',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [],
                'itemsProcFunc' => \TRAW\VideoVtt\FormEngine\ControlsList::class . '->itemsProcFunc',
                'cols' => '3',
                'default' => 0,
            ],
        ],
        'picinpic' => [
            'exclude' => true,
            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.picinpic',
            'displayCond' => 'USER:TRAW\\VideoVtt\\Resource\\DisplayCondition\\PicInPicDisplayCondition->displayPicInPic',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
            ],
        ],
        'poster' => [
            'exclude' => true,
            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.poster',
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
        'start_time' => [
            'exclude' => true,
            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.start_time',
            'displayCond' => 'USER:TRAW\\VideoVtt\\Resource\\DisplayCondition\\TimeDisplayCondition->displayStartField',
            'config' => [
                'type' => 'number',
                'format' => 'integer',
                'range' => [
                    'lower' => 0,
                ],
                'default' => 0,
                'size' => '4',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'end_time' => [
            'exclude' => true,
            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.end_time',
            'displayCond' => 'USER:TRAW\\VideoVtt\\Resource\\DisplayCondition\\TimeDisplayCondition->displayEndField',
            'config' => [
                'type' => 'number',
                'format' => 'integer',
                'range' => [
                    'lower' => 0,
                ],
                'default' => 0,
                'size' => '4',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'lang' => [
            'exclude' => true,
            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.lang',
            'displayCond' => 'USER:TRAW\\VideoVtt\\Resource\\DisplayCondition\\TextTrackDisplayCondition->displayCaptionLanguageField',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'itemsProcFunc' => \TRAW\VideoVtt\Utility\SiteLanguageUtility::class . '->getIsoTwoLetterCodes',
                'items' => [
                    ['label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.lang.none', 'value' => ''],
                ],
            ],
        ],
    ]);
    //changed order of fields
    $GLOBALS['TCA'][$table]['palettes']['videoOverlayPalette']['showitem']
        = 'title,description,
        --linebreak--,link,
        --linebreak--,poster,
        --linebreak--,autoplay,mute,loop,lang,
        --linebreak--,showinfo,controls,start_time,end_time,
        --linebreak--,controlslist,picinpic';

    $GLOBALS['TCA'][$table]['palettes']['basicoverlayPalette']['showitem']
        = 'title,description,
    --linebreak--,track_default,--linebreak--,track_label,track_language,track_type';
});
