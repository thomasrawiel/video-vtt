<?php

declare(strict_types=1);

namespace TRAW\VideoVtt\Resource\DisplayCondition;

/*
 * This file is part of the "video_vtt" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Class TextTrackDisplayCondition
 */
class TextTrackDisplayCondition extends AbstractDisplayCondition
{
    public function displayTracksField(array $data): bool
    {
        $fileUid = $this->getFileUid($data);

        return $this->isLocalVideo($fileUid);
    }

    public function displayCaptionLanguageField(array $data): bool
    {
        $fileUid = $this->getFileUid($data);

        return $this->isYoutubeVideo($fileUid);
    }
}
