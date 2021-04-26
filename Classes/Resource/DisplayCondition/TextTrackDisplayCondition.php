<?php
declare(strict_types=1);

namespace TRAW\VideoVtt\Resource\DisplayCondition;

use LINGNER\LinTemplate\Resource\Rendering\VideoTagRenderer;
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