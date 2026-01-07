<?php
declare(strict_types=1);

namespace TRAW\VideoVtt\Resource\Rendering;

use TRAW\VideoVtt\Utility\PosterImageUtility;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
        $options['loop'] = $file->getProperty('loop');

        $audioTag = parent::render($file, $width, $height, $options);

        $posterImage = PosterImageUtility::getPosterImage($file, false);

        $imageTag = '';

        if($posterImage instanceof FileReference) {
            $processedImage = PosterImageUtility::getCropVariant($posterImage);
            $imageTag = sprintf(
                '<img class="audio-poster" alt="%s" src="%s" width="%s" height="%s" />',
                $posterImage->getProperty('alternative'),
                $processedImage->getPublicUrl(),
                $processedImage->getProperty('width'),
                $processedImage->getProperty('height')

            );
        }

        return $imageTag.$audioTag;
    }
}
