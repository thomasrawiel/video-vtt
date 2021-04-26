<?php

namespace TRAW\VideoVtt\Resource\Rendering;

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
        $options['no-cookie'] = true;

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

        $youTubeUrl = sprintf(
            'https://www.youtube%s.com/embed/%s?%s',
            !isset($options['no-cookie']) || !empty($options['no-cookie']) ? '-nocookie' : '',
            $videoId,
            implode('&amp;', $urlParams)
        );

        return $youTubeUrl;
    }
}
