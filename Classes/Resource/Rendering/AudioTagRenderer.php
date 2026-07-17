<?php
declare(strict_types=1);

namespace TRAW\VideoVtt\Resource\Rendering;

use Psr\Http\Message\ServerRequestInterface;
use TRAW\VideoVtt\Options\Options;
use TRAW\VideoVtt\Utility\PosterImageUtility;
use TRAW\VideoVtt\Utility\FileUtility;
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
        $options = new Options($file, $options);

        $posterImageUtility = GeneralUtility::makeInstance(PosterImageUtility::class);
        $posterImage = $posterImageUtility->getPosterImage($file, false);

        $imageTag = '';

        if ($posterImage instanceof FileReference) {
            $processedImage = $posterImageUtility->getCropVariant($posterImage);
            $imageTag = sprintf(
                '<img class="audio-poster" alt="%s" src="%s" width="%s" height="%s" />',
                $posterImage->getProperty('alternative'),
                $processedImage->getPublicUrl(),
                $processedImage->getProperty('width'),
                $processedImage->getProperty('height')

            );
        }

        $attributes = [];
        if ($options->getAdditionalAttributes() !== []) {
            $attributes[] = GeneralUtility::implodeAttributes($options->getAdditionalAttributes(), true, true);
        }
        if ($options->getData() !== []) {
            $data = $options->getData();
            array_walk($data, static function (string &$value, string $key): void {
                $value = 'data-' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
            });
            $attributes[] = implode(' ', $data);
        }
        if ($options->getControls()) {
            $attributes[] = 'controls';
        }
        if ($options->getAutoPlay()) {
            $attributes[] = 'autoplay';
            $attributes[] = 'muted';
        }
        if ($options->getMute() && !$options->getAutoPlay()) {
            $attributes[] = 'muted';
        }
        if ($options->getLoop()) {
            $attributes[] = 'loop';
        }
        if ($options->getControlsList()) {
            $controlsList = $options->getControlsListValueAudio();
            $attributes[] = 'controlsList="' . htmlspecialchars($controlsList) . '"';
        }
        foreach (['class', 'dir', 'id', 'lang', 'style', 'title', 'accesskey', 'tabindex', 'onclick', 'preload'] as $key) {
            if (!empty($options->get($key))) {
                $attributes[] = $key . '="' . htmlspecialchars((string)$options->get($key)) . '"';
            }
        }

        $source = htmlspecialchars($this->getSource($file));

        $start = $options->getStartTime();
        if ($start < 0) {
            $start = 0;
        }
        $sourceParams = [$start];

        $end = $options->getEndTime();
        if ($end > $start) {
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
