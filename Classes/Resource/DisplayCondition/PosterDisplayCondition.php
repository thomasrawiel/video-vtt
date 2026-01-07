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
 * Class PosterDisplayCondition
 */
class PosterDisplayCondition extends AbstractDisplayCondition
{
    public function displayPoster(array $data): bool
    {
        $fileUid = $this->getFileUid($data);

        return $this->isLocalVideo($fileUid)
            || $this->isLocalAudio($fileUid);
    }
}
