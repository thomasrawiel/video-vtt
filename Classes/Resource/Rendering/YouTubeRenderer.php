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

use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * YouTube renderer class
 */
class YouTubeRenderer extends \TYPO3\CMS\Core\Resource\Rendering\YouTubeRenderer
{
    #[\Override]
    protected function createYouTubeUrl(array $options, FileInterface $file): string
    {
        $videoId = $this->getVideoIdFromFile($file);

        if (empty($videoId)) {
            $orgFile = $file instanceof FileReference ? $file->getOriginalFile() : $file;

            throw new \Exception('Referenced file "' . $orgFile->getIdentifier() . '" not found.', 9498323370);
        }

        $options['autoplay'] = $file->getProperty('autoplay');
        $options['loop'] = $file->getProperty('loop');
        $options['mute'] = $file->getProperty('mute');
        $options['controls'] = $file->getProperty('controls');
        $options['showinfo'] = $file->getProperty('showinfo');

        $urlParams = [
            'autohide=1',
            'modestbranding=1',
            'playsinline=1',
            'rel=0',
            'controls=' . $options['controls'],
            'showinfo=' . $options['showinfo'],
            'enablejsapi=1&origin=' . rawurlencode(GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST')),
        ];

        if (!empty($options['autoplay'])) {
            $urlParams[] = 'autoplay=1';
            // If autoplay is enabled, enforce mute=1, see https://developer.chrome.com/blog/autoplay/
            $urlParams[] = 'mute=1';
        }

        if (!empty($options['mute']) && empty($options['autoplay'])) {
            $urlParams[] = 'mute=1';
        }

        if ($options['loop']) {
            $urlParams[] = 'loop=1&playlist=' . rawurlencode($videoId);
        }

        return sprintf(
            'https://www.youtube-nocookie.com/embed/%s?%s',
            rawurlencode($videoId),
            implode('&', $urlParams)
        );
    }
}
