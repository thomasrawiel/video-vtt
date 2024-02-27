<?php

defined('TYPO3') || die('Access denied.');

call_user_func(function ($_EXTKEY = 'video_vtt', $table = 'sys_file_metadata') {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns($table, [
        'tracks' => [
            'exclude' => true,
            'label' => 'LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file.tracks',
            'displayCond' => 'USER:TRAW\\VideoVtt\\Resource\\DisplayCondition\\TextTrackDisplayCondition->displayTracksField',
            'config' => [
                'type' => 'file',
                'allowed' => 'vtt',
            ],
        ],
    ]);
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        $table,
        'tracks',
        (string)\TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO,
        'after:duration'
    );
});
