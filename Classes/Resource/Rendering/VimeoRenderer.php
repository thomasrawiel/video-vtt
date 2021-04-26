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

/**
 * Vimeo renderer class
 */
class VimeoRenderer extends \TYPO3\CMS\Core\Resource\Rendering\VimeoRenderer
{

    /**
     * @param array $options
     * @param FileInterface $file
     * @return string
     */
    protected function createVimeoUrl(array $options, FileInterface $file)
    {
        $videoId = $this->getVideoIdFromFile($file);


        //i dont care
        $options = [
            'autoplay' => $file->getProperty('autoplay'),
            'loop' => $file->getProperty('loop'),
            'mute' => $file->getProperty('mute'),
            'controls' => $file->getProperty('controls'),
            'showinfo' => $file->getProperty('showinfo'),
            'no-cookie' => true, //always true
        ];

        $urlParams = [];
        $urlParams[] = 'autoplay=' . $options['autoplay'];
        $urlParams[] = 'muted=' . $options['mute'];
        $urlParams[] = 'loop='.$options['loop'];
        $urlParams[] = 'title=' . $options['showinfo'];
        $urlParams[] = 'byline=' . $options['showinfo'];
        $urlParams[] = 'playsinline=1';

        $urlParams[] = 'portrait=0';

        return sprintf('https://player.vimeo.com/video/%s?%s', $videoId, implode('&amp;', $urlParams));
    }
}
