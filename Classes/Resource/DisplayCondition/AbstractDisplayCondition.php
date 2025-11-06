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
use TRAW\VideoVtt\Resource\Rendering\VimeoRenderer;
use TRAW\VideoVtt\Resource\Rendering\YouTubeRenderer;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class AbstractDisplayCondition
 */
class AbstractDisplayCondition
{
    /**
     * The field should be rendered if the file can be rendered with the VideoTagRenderer
     */
    protected function fieldShouldBeRendered(int $fileUid): bool
    {
        if ($fileUid > 0) {
            $file = (GeneralUtility::makeInstance(FileRepository::class))->findByUid($fileUid);
            return in_array(
                $file->getMimeType(),
                (GeneralUtility::makeInstance(VideoTagRenderer::class))->getPossibleMimeTypes(),
                true
            );
        }

        return false;
    }

    public function isLocalVideo(int $fileUid): bool
    {
        if ($fileUid > 0) {
            $file = (GeneralUtility::makeInstance(FileRepository::class))->findByUid($fileUid);
            return GeneralUtility::makeInstance(VideoTagRenderer::class)->canRender($file);
        }

        return false;
    }

    public function isYoutubeVideo(int $fileUid): bool
    {
        if ($fileUid > 0) {
            $file = (GeneralUtility::makeInstance(FileRepository::class))->findByUid($fileUid);
            return GeneralUtility::makeInstance(YouTubeRenderer::class)->canRender($file);
        }

        return false;
    }

    public function isVimeoVideo(int $fileUid): bool
    {
        if ($fileUid > 0) {
            $file = (GeneralUtility::makeInstance(FileRepository::class))->findByUid($fileUid);
            return GeneralUtility::makeInstance(VimeoRenderer::class)->canRender($file);
        }

        return false;
    }

    protected function getFileUid(array $data): int
    {
        $record = $data['record'];
        $fileUid = 0;
        if (isset($record['file']) || (isset($record['uid_local'][0]) && $record['uid_local'][0]['table'] === 'sys_file')) {
            $fileUid = (int)($record['file'][0] ?? $record['uid_local'][0]['uid'] ?? 0);
        }

        return $fileUid;
    }
}
