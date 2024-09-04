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

/**
 * Class AbstractDisplayCondition
 * @package TRAW\VideoVtt\Resource\DisplayCondition
 */
class AbstractDisplayCondition
{
    /**
     * The field should be rendered if the file can be rendered with the VideoTagRenderer
     *
     * @param int $fileUid
     *
     * @return bool
     */
    protected function fieldShouldBeRendered(int $fileUid): bool
    {
        if ($fileUid > 0) {
            $file = (GeneralUtility::makeInstance(FileRepository::class))->findByUid($fileUid);
            return in_array($file->getMimeType(),
                (GeneralUtility::makeInstance(VideoTagRenderer::class))->getPossibleMimeTypes(),
                true
            );
        }
        return false;
    }
}