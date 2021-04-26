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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * YouTube renderer class
 */
class YouTubeRenderer extends \TYPO3\CMS\Core\Resource\Rendering\YouTubeRenderer
{
    /**
     * @param array $options
     * @param FileInterface $file
     * @return string
     */
    protected function createYouTubeUrl(array $options, FileInterface $file)
    {
        $videoId = $this->getVideoIdFromFile($file);

        $options['autoplay'] = $file->getProperty('autoplay');
        $options['loop'] = $file->getProperty('loop');
        $options['mute'] = $file->getProperty('mute');
        $options['controls'] = $file->getProperty('controls');
        $options['showinfo'] = $file->getProperty('showinfo');
        $options['no-cookie'] = true; //always use no-cookie domain

        $urlParams = [
            'autohide=1',
            'modestbranding=1',
            'playsinline=1',
            'rel=0',
            'autoplay=' . $options['autoplay'],
            'mute=' . $options['mute'],
            'controls=' . $options['controls'],
            'showinfo=' . $options['showinfo'],
            'enablejsapi=1&amp;origin=' . rawurlencode(GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST')),
        ];

        if ($options['loop']) {
            $urlParams[] = 'loop=1&amp;playlist=' . $videoId;
        }

        return sprintf(
            'https://www.youtube%s.com/embed/%s?%s',
            !isset($options['no-cookie']) || !empty($options['no-cookie']) ? '-nocookie' : '',
            $videoId,
            implode('&amp;', $urlParams)
        );
    }
}
