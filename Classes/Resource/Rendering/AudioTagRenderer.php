<?php
declare(strict_types=1);

namespace TRAW\VideoVtt\Resource\Rendering;

use Psr\Http\Message\ServerRequestInterface;
use TRAW\VideoVtt\Utility\PosterImageUtility;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Service\ImageService;

class AudioTagRenderer extends \TYPO3\CMS\Core\Resource\Rendering\AudioTagRenderer
{
    public function getPriority(): int
    {
        return 7;
    }

    public function render(FileInterface $file, $width, $height, array $options = [])
    {
        $options['autoplay'] = $file->getProperty('autoplay');
        $options['muted'] = (bool)$options['autoplay'] ? '1' : $file->getProperty('mute');
        $options['controls'] = $file->getProperty('controls');
        $options['controlsList'] = $file->getProperty('controlslist');
        $options['loop'] = $file->getProperty('loop');

        $posterImage = PosterImageUtility::getPosterImage($file, false);

        $imageTag = '';

        if ($posterImage instanceof FileReference) {
            $processedImage = PosterImageUtility::getCropVariant($posterImage);
            $imageTag = sprintf(
                '<img class="audio-poster" alt="%s" src="%s" width="%s" height="%s" />',
                $posterImage->getProperty('alternative'),
                $processedImage->getPublicUrl(),
                $processedImage->getProperty('width'),
                $processedImage->getProperty('height')

            );
        }

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
            array_walk($options['data'], static function (string &$value, string $key): void {
                $value = 'data-' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
            });
            $attributes[] = implode(' ', $options['data']);
        }
        if (!isset($options['controls']) || !empty($options['controls'])) {
            $attributes[] = 'controls';
        }
        if (!empty($options['autoplay'])) {
            $attributes[] = 'autoplay';
        }
        if (!empty($options['muted'])) {
            $attributes[] = 'muted';
        }
        if (!empty($options['loop'])) {
            $attributes[] = 'loop';
        }
        if (!empty($options['controlsList']) && $options['controlsList'] > 0) {
            $controlsList = [
                1 => 'nodownload',
                2 => 'noplaybackrate',
                3 => 'nodownload noplaybackrate',
            ];
            $options['controlsList'] = $controlsList[$options['controlsList']];
        }
        foreach (['class', 'dir', 'id', 'lang', 'style', 'title', 'accesskey', 'tabindex', 'onclick', 'preload', 'controlsList'] as $key) {
            if (!empty($options[$key])) {
                $attributes[] = $key . '="' . htmlspecialchars((string)$options[$key]) . '"';
            }
        }

        $source = htmlspecialchars($this->getSource($file, $usedPathsRelativeToCurrentScript));

        $start = (int)$file->getProperty('start_time');
        if ($start < 0) {
            $start = 0;
        }
        $end = (int)$file->getProperty('end_time');

        $sourceParams = [$start];
        if ($end > 0) {
            $sourceParams[] = $end;
        }

        $sourceTime = sprintf('#t=%s', implode(',', $sourceParams));

        return $imageTag . sprintf(
                '<audio%s><source src="%s%s" type="%s"></audio>',
                empty($attributes) ? '' : ' ' . implode(' ', $attributes),
                $source,
                $sourceTime,
                $file->getMimeType()
            );

        return $imageTag . $audioTag;
    }

    protected function getSource(FileInterface $file): string
    {
        $source = (string)$file->getPublicUrl();

        // We need an absolute path for the backend
        if (($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface
            && \TYPO3\CMS\Core\Http\ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()) {
            return PathUtility::getAbsoluteWebPath($source);
        }

        return $source;
    }
}
