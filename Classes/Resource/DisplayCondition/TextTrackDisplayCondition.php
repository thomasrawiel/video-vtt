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

use TRAW\VideoVtt\Resource\Rendering\VideoTagRenderer;
use TYPO3\CMS\Core\Resource\FileRepository;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class TextTrackDisplayCondition
{
    public function displayTracksField(array $data): bool
    {
        $record = $data['record'];
        $fileUid = 0;
        if (isset($record['file'])) {
            $fileUid = (int)$record['file'][0];
        }
        if ($fileUid > 0) {
            $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
            $videoTagRenderer = GeneralUtility::makeInstance(VideoTagRenderer::class);
            $file = $fileRepository->findByUid($fileUid);
            return in_array($file->getMimeType(), $videoTagRenderer->getPossibleMimeTypes(), true);
        }
        return false;
    }
}