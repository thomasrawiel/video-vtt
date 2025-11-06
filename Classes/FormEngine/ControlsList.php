<?php
declare(strict_types=1);

namespace TRAW\VideoVtt\FormEngine;

use TRAW\VideoVtt\Resource\DisplayCondition\ControlsListDisplayCondition;
use TYPO3\CMS\Core\Localization\LanguageService;

class ControlsList
{
    private readonly ControlsListDisplayCondition $displayCondition;

    public function __construct() {
        $this->displayCondition = new ControlsListDisplayCondition();
    }

    public function itemsProcFunc(&$params): void
    {
        if(($params['table'] ?? '') !== 'sys_file_reference') {
            return;
        }

        $fileUid = (int)($params['row']['uid_local'] ?? 0);

        if($fileUid === 0) {
            return;
        }

        $download = ['label' => $this->getLanguageService()->sL('LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.controlsList.download'), 'invertStateDisplay' => true];
        $playbackRate = ['label' => $this->getLanguageService()->sL('LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.controlsList.playbackrate'), 'invertStateDisplay' => true];
        $remotePlayback = ['label' => $this->getLanguageService()->sL('LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.controlsList.remoteplayback'), 'invertStateDisplay' => true];
        $fullscreen = ['label' => $this->getLanguageService()->sL('LLL:EXT:video_vtt/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.controlsList.fullscreen'), 'invertStateDisplay' => true];

        $mp4 = [
            $download,
            $playbackRate,
            $fullscreen,
            $remotePlayback,
        ];

        $ytVim = [
            $fullscreen
        ];

       if($this->displayCondition->isYoutubeVideo($fileUid) || $this->displayCondition->isVimeoVideo($fileUid)) {
           $params['items'] = $ytVim;
           return;
       }

       $params['items'] = $mp4;
    }

    private function getLanguageService(): LanguageService {
        return $GLOBALS['LANG'];
    }
}
