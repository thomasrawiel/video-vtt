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

use TRAW\VideoVtt\Options\Options;
use TRAW\VideoVtt\Utility\DurationUtility;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Vimeo renderer class
 */
class VimeoRenderer extends \TYPO3\CMS\Core\Resource\Rendering\VimeoRenderer
{
    public function getPriority(): int
    {
        return 7;
    }

    #[\Override]
    protected function collectIframeAttributes($width, $height, array $options)
    {
        $attributes = parent::collectIframeAttributes(...func_get_args());

        $attributes['referrerpolicy'] = 'strict-origin-when-cross-origin';

        if (isset($options['file']) && $options['file']->getProperty('controlslist') === 1) {
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
    protected function createVimeoUrl(array $options, FileInterface $file): string
    {
        $videoIdRaw = $this->getVideoIdFromFile($file);
        $videoIdRaw = GeneralUtility::trimExplode('/', $videoIdRaw, true);

        $videoId = $videoIdRaw[0] ?? null;
        $hash = $videoIdRaw[1] ?? null;

        $options = new Options($file, $options);

        if (empty($videoId)) {
            $orgFile = $file instanceof FileReference ? $file->getOriginalFile() : $file;

            throw new \Exception('Referenced file "' . $orgFile->getIdentifier() . '" not found.', 6631073425);
        }

        $urlParams = [];
        if (!empty($hash)) {
            $urlParams[] = 'h=' . $hash;
        }

        if ($options->getAutoPlay()) {
            $urlParams[] = 'autoplay=1';
            // If autoplay is enabled, enforce muted=1, see https://developer.chrome.com/blog/autoplay/
            $urlParams[] = 'muted=1';
        }

        if ($options->getMute() && !$options->getAutoPlay()) {
            $urlParams[] = 'muted=1';
        }

        if ($options->getLoop()) {
            $urlParams[] = 'loop=1';
        }

        if ((int)$options->get('api') === 1) {
            $urlParams[] = 'api=1';
        }

        if ($options->get('no-cookie')) {
            $urlParams[] = 'dnt=1';
        }

        $urlParams[] = 'controls=' . $options->getControls();
        $urlParams[] = 'pip=' . $options->getPicinpic();
        $urlParams[] = 'title=' . $options->getShowInfo();
        $urlParams[] = 'byline=' . $options->getShowInfo();
        $urlParams[] = 'portrait=0';

        $start = $options->getStartTime();
        $end = $options->getEndTime();

        $urlAnchors = [];
        //#t=1m30s&end=3m10s

        if ($start > 0) {
            $urlAnchors[] = 't=' . DurationUtility::formatDuration($start);
        }
        if ($end > $start) {
            $urlAnchors[] = 'end=' . DurationUtility::formatDuration($end);
        }

        $embedUrl = sprintf('https://player.vimeo.com/video/%s?%s', $videoId, implode('&', $urlParams));

        if (count($urlAnchors)) {
            $embedUrl = $embedUrl . '#' . implode('&', $urlAnchors);
        }

        return $embedUrl;
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
    public function render(FileInterface $file, $width, $height, array $options = []): string
    {
        if (($options['returnUrl'] ?? false) === true) {
            $options = $this->collectOptions($options, $file);
            $src = $this->createVimeoUrl($options, $file);
            return htmlspecialchars($src, ENT_QUOTES | ENT_HTML5);
        }
        return parent::render(...func_get_args());
    }


}
