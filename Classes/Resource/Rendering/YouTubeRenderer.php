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
    public function getPriority(): int
    {
        return 7;
    }

    #[\Override]
    protected function collectIframeAttributes($width, $height, array $options)
    {
        $attributes = parent::collectIframeAttributes(...func_get_args());

        //fix for youtube error 153
        $attributes['referrerpolicy'] = 'strict-origin-when-cross-origin';

        if ($options['file']->getProperty('controlslist') === 1) {
            unset($attributes['allowfullscreen']);
            if (!empty($attributes['allow'])) {
                $values = preg_split('/[\s;]+/', $attributes['allow']);
                $values = array_filter($values, static fn(string $v): bool => strtolower($v) !== 'fullscreen');
                $attributes['allow'] = implode('; ', $values);
                if ($attributes['allow'] === '') {
                    unset($attributes['allow']);
                }
            }
        }

        return $attributes;
    }

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
        $options['lang'] = $file->getProperty('lang');
        $options['controlsList'] = $file->getProperty('controlslist');

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

        $start = $file->getProperty('start_time');
        $end = $file->getProperty('end_time');

        if ($start > 0) {
            $urlParams[] = 'start=' . $start;
        }
        if ($end > 0) {
            $urlParams[] = 'end=' . $end;
        }

        if (!empty($options['lang'])) {
            $urlParams[] = 'cc_load_policy=1';
            $urlParams[] = 'hl=' . $options['lang'];
            $urlParams[] = 'cc_lang_pref=' . $options['lang'];
        }

        if (isset($options['controlsList'])) {
            $urlParams[] = 'fs=' . ((int)(!$options['controlsList']));
        }

        return sprintf(
            'https://www.youtube-nocookie.com/embed/%s?%s',
            rawurlencode($videoId),
            implode('&', $urlParams)
        );
    }

    private function getControlsListAttributes(FileInterface $file): array
    {

    }

    /**
     * Render for given File(Reference) html output
     *
     * @param FileInterface $file
     * @param int|string    $width  TYPO3 known format; examples: 220, 200m or 200c
     * @param int|string    $height TYPO3 known format; examples: 220, 200m or 200c
     * @param array         $options
     *
     * @return string
     */
    #[\Override]
    public function render(FileInterface $file, $width, $height, array $options = []): string
    {
        if (($options['returnUrl'] ?? false) === true) {
            $options = $this->collectOptions($options, $file);
            $src = $this->createYouTubeUrl($options, $file);
            return htmlspecialchars($src, ENT_QUOTES | ENT_HTML5);
        }
        return parent::render(...func_get_args());
    }
}
