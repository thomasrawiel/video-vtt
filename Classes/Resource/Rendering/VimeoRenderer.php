<?php

namespace TRAW\VideoVtt\Resource\Rendering;

/*
 * This file is part of the "video_vtt" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TRAW\VideoVtt\Utility\DurationUtility;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Vimeo renderer class
 */
class VimeoRenderer extends \TYPO3\CMS\Core\Resource\Rendering\VimeoRenderer
{
    #[\Override]
    protected function collectOptions(array $options, FileInterface $file): array
    {
        // Check for an autoplay option at the file reference itself, if not overridden yet.
        if (!isset($options['autoplay']) && $file instanceof FileReference) {
            $autoplay = $file->getProperty('autoplay');
            if ($autoplay !== null) {
                $options['autoplay'] = $autoplay;
            }
        }

        /** @extensionScannerIgnoreLine */
        if (!isset($options['allow'])) {
            /** @extensionScannerIgnoreLine */
            $options['allow'] = 'fullscreen';
            if (!empty($options['autoplay'])) {
                /** @extensionScannerIgnoreLine */
                $options['allow'] = 'autoplay; fullscreen';
            }
        }

        if (!isset($options['picinpic'])) {
            $pip = $file->getProperty('picinpic');
            if ($pip !== null) {
                $options['picinpic'] = $pip;
            }
        }

        if (!isset($options['controls'])) {
            $controls = $file->getProperty('controls');
            if ($controls !== null) {
                $options['controls'] = $controls;
            }
        }

        return $options;
    }

    #[\Override]
    protected function createVimeoUrl(array $options, FileInterface $file): string
    {
        $videoIdRaw = $this->getVideoIdFromFile($file);
        $videoIdRaw = GeneralUtility::trimExplode('/', $videoIdRaw, true);

        $videoId = $videoIdRaw[0] ?? null;
        $hash = $videoIdRaw[1] ?? null;

        if (empty($videoId)) {
            $orgFile = $file instanceof FileReference ? $file->getOriginalFile() : $file;

            throw new \Exception('Referenced file "' . $orgFile->getIdentifier() . '" not found.', 6631073425);
        }

        $urlParams = [];
        if (!empty($hash)) {
            $urlParams[] = 'h=' . $hash;
        }

        if (!empty($options['autoplay'])) {
            $urlParams[] = 'autoplay=1';
            // If autoplay is enabled, enforce muted=1, see https://developer.chrome.com/blog/autoplay/
            $urlParams[] = 'muted=1';
        }

        if (!empty($options['loop'])) {
            $urlParams[] = 'loop=1';
        }

        if (isset($options['api']) && (int)$options['api'] === 1) {
            $urlParams[] = 'api=1';
        }

        if (!isset($options['no-cookie']) || !empty($options['no-cookie'])) {
            $urlParams[] = 'dnt=1';
        }

        $urlParams[] = 'controls=' . (int)!empty($options['controls']);
        $urlParams[] = 'pip=' . (int)!empty($options['picinpic']);
        $urlParams[] = 'title=' . (int)!empty($options['showinfo']);
        $urlParams[] = 'byline=' . (int)!empty($options['showinfo']);
        $urlParams[] = 'portrait=0';

        $start = $file->getProperty('start_time');
        $end = $file->getProperty('end_time');

        $urlAnchors = [];
        //#t=1m30s&end=3m10s

        if ($start > 0) {
            $urlAnchors[] = 't=' . DurationUtility::formatDuration($start);
        }
        if ($end > 0 && $end > $start) {
            $urlAnchors[] = 'end=' . DurationUtility::formatDuration($end);
        }

        $embedUrl = sprintf('https://player.vimeo.com/video/%s?%s', $videoId, implode('&', $urlParams));

        if(count($urlAnchors)) {
            $embedUrl = $embedUrl . '#' .implode('&', $urlAnchors);
        }

        return $embedUrl;
    }
}
