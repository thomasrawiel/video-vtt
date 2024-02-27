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

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

/**
 * Class VideoTagRenderer
 */
class VideoTagRenderer extends \TYPO3\CMS\Core\Resource\Rendering\VideoTagRenderer
{
    /**
     * Render for given File(Reference) HTML output
     *
     * @param FileInterface $file
     * @param int|string $width TYPO3 known format; examples: 220, 200m or 200c
     * @param int|string $height TYPO3 known format; examples: 220, 200m or 200c
     * @param array $options controls = TRUE/FALSE (default TRUE), autoplay = TRUE/FALSE (default FALSE), loop = TRUE/FALSE (default FALSE)
     * @param bool $usedPathsRelativeToCurrentScript See $file->getPublicUrl()
     * @return string
     */
    public function render(FileInterface $file, $width, $height, array $options = [], $usedPathsRelativeToCurrentScript = false)
    {
        //take all options from file reference
        $options['autoplay'] = $file->getProperty('autoplay');
        $options['muted'] = $file->getProperty('mute');
        $options['controls'] = $file->getProperty('controls');
        $options['loop'] = $file->getProperty('loop');

        // If autoplay isn't set manually check if $file is a FileReference take autoplay from there
        if (!isset($options['autoplay']) && $file instanceof FileReference) {
            $autoplay = $file->getProperty('autoplay');
            if ($autoplay !== null) {
                $options['autoplay'] = $autoplay;
            }
        }

        $attributes = [];
        if (isset($options['additionalAttributes']) && is_array($options['additionalAttributes'])) {
            $attributes[] = GeneralUtility::implodeAttributes($options['additionalAttributes'], true, true);
        }
        if (isset($options['data']) && is_array($options['data'])) {
            array_walk($options['data'], function (&$value, $key) {
                $value = 'data-' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
            });
            $attributes[] = implode(' ', $options['data']);
        }
        if ((int)$width > 0) {
            $attributes[] = 'width="' . (int)$width . '"';
        }
        if ((int)$height > 0) {
            $attributes[] = 'height="' . (int)$height . '"';
        }
        if (!isset($options['controls']) || !empty($options['controls'])) {
            $attributes[] = 'controls';
        }
        if (!empty($options['autoplay'])) {
            $attributes[] = 'autoplay';
            $attributes[] = 'playsinline';
        }
        if (!empty($options['muted'])) {
            $attributes[] = 'muted';
        }
        if (!empty($options['loop'])) {
            $attributes[] = 'loop';
        }
        if (isset($options['additionalConfig']) && is_array($options['additionalConfig'])) {
            foreach ($options['additionalConfig'] as $key => $value) {
                if ((bool)$value) {
                    $attributes[] = htmlspecialchars($key);
                }
            }
        }

        foreach (['class', 'dir', 'id', 'lang', 'style', 'title', 'accesskey', 'tabindex', 'onclick', 'controlsList', 'preload'] as $key) {
            if (!empty($options[$key])) {
                $attributes[] = $key . '="' . htmlspecialchars($options[$key]) . '"';
            }
        }

        // Clean up duplicate attributes
        $attributes = array_unique($attributes);

        return sprintf(
            '<video%s><source src="%s" type="%s">%s</video>',
            empty($attributes) ? '' : ' ' . implode(' ', $attributes),
            htmlspecialchars($this->getSource($file, $usedPathsRelativeToCurrentScript)),
            $file->getMimeType(),
            $this->getTracks($file)
        );
    }

    /**
     * The MimeTypes are used in a DisplayCondition
     *
     * @return array
     */
    public function getPossibleMimeTypes(): array
    {
        return $this->possibleMimeTypes;
    }

    /**
     * @param FileInterface $file
     * @param bool $usedPathsRelativeToCurrentScript
     * @return string
     */
    protected function getSource(FileInterface $file, bool $usedPathsRelativeToCurrentScript): string
    {
        $source = (string)$file->getPublicUrl();

        // We need an absolute path for the backend
        if (($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface
            && ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()
        ) {
            $source = PathUtility::getAbsoluteWebPath($source);
        }

        return $source;
    }

    /**
     * @param FileInterface $file
     * @return string
     */
    protected function getTracks(FileInterface $file): string
    {
        $tracks = '';
        $originalFile = $file;
        if ($file instanceof FileReference) {
            $originalFile = $file->getOriginalFile();
        }
        if ($originalFile->getProperty('tracks')) {
            $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
            $relatedFiles = $fileRepository->findByRelation(
                'sys_file_metadata',
                'tracks',
                $originalFile->getMetaData()['uid']
            );

            foreach ($relatedFiles ?? [] as $key => $fileObject) {
                $trackLanguage = $fileObject->getProperty('track_language') ?: '';
                $trackType = $fileObject->getProperty('track_type') ?: 'subtitles';
                $trackLabel = $fileObject->getProperty('track_label') ?: '';
                $trackDefault = $fileObject->getProperty('track_default') ?: false;
                $publicUrl = $this->getSource($fileObject, false);

                if ($this->canRenderTrack($publicUrl, $trackType, $trackLanguage)) {
                    $trackTag = new TagBuilder('track');
                    $trackTag->addAttribute('src', $publicUrl);
                    $trackTag->addAttribute('kind', $trackType);
                    if (!empty($trackLanguage)) {
                        $trackTag->addAttribute('srclang', $trackLanguage);
                    }
                    if (!empty($trackLabel)) {
                        $trackTag->addAttribute('label', $trackLabel);
                    }
                    if ($trackDefault) {
                        $trackTag->addAttribute('default', 'default');
                    }
                    $tracks .= $trackTag->render();
                }
            }
        }

        return $tracks;
    }

    /**
     * @param string $publicUrl
     * @param string $trackType
     * @param string $trackLanguage
     * @return bool
     */
    protected function canRenderTrack(string $publicUrl, string $trackType, string $trackLanguage): bool
    {
        if (empty($publicUrl)) {
            return false;
        }
        //dont render subtitles without a track language
        if ($trackType === 'subtitles' && empty($trackLanguage)) {
            return false;
        }

        return true;
    }
}
