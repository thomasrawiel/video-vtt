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
use TRAW\VideoVtt\Utility\CoreUtility;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

/**
 * Class VideoTagRenderer
 */
class VideoTagRenderer extends \TYPO3\CMS\Core\Resource\Rendering\VideoTagRenderer
{
    public function getPriority(): int
    {
        return 7;
    }

    /**
     * Render for given File(Reference) HTML output
     *
     * @param int|string $width                               TYPO3 known format; examples: 220, 200m or 200c
     * @param int|string $height                              TYPO3 known format; examples: 220, 200m or 200c
     * @param array      $options                             controls = TRUE/FALSE (default TRUE), autoplay =
     *                                                        TRUE/FALSE (default FALSE), loop = TRUE/FALSE (default
     *                                                        FALSE)
     * @param bool       $usedPathsRelativeToCurrentScript    See $file->getPublicUrl()
     */
    #[\Override]
    public function render(FileInterface $file, $width, $height, array $options = [], $usedPathsRelativeToCurrentScript = false): string
    {
        //take all options from file reference
        $options['autoplay'] = $file->getProperty('autoplay');
        $options['muted'] = $file->getProperty('mute');
        $options['controls'] = $file->getProperty('controls');
        $options['controlsList'] = $file->getProperty('controlslist');
        $options['picinpic'] = $file->getProperty('picinpic');
        $options['loop'] = $file->getProperty('loop');

        // If autoplay isn't set manually check if $file is a FileReference take autoplay from there
        if (empty($options['autoplay']) && $file instanceof FileReference) {
            $options['autoplay'] = $file->getProperty('autoplay') ?? '0';
        }

        $attributes = [];
        if (isset($options['additionalAttributes']) && is_array($options['additionalAttributes'])) {
            $attributes[] = GeneralUtility::implodeAttributes($options['additionalAttributes'], true, true);
        }

        if (isset($options['data']) && is_array($options['data'])) {
            array_walk($options['data'], function (&$value, $key): void {
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

        if (isset($options['picinpic']) && $options['picinpic'] === 0) {
            $attributes[] = 'disablePictureInPicture';
        }

        if (!empty($options['controlsList']) && $options['controlsList'] > 0) {
            $controlsList = [
                1 => 'nodownload',
                2 => 'noplaybackrate',
                4 => 'nofullscreen',
                8 => 'noremoteplayback',
                3 => 'nodownload noplaybackrate',
                5 => 'nodownload nofullscreen',
                9 => 'nodownload noremoteplayback',
                6 => 'noplaybackrate nofullscreen',
                10 => 'noplaybackrate noremoteplayback',
                12 => 'nofullscreen noremoteplayback',
                7 => 'nodownload noplaybackrate nofullscreen',
                11 => 'nodownload noplaybackrate noremoteplayback',
                13 => 'nodownload nofullscreen noremoteplayback',
                14 => 'noplaybackrate nofullscreen noremoteplayback',
                15 => 'nodownload noplaybackrate nofullscreen noremoteplayback',
            ];
            $attributes[] = 'controlsList="' . $controlsList[$options['controlsList']] . '"';
        }

        if (!empty($options['autoplay'])) {
            $attributes[] = 'autoplay';
            $attributes[] = 'playsinline';
            $attributes[] = 'muted';
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
                    $attributes[] = htmlspecialchars((string)$key);
                }
            }
        }

        foreach (['class', 'dir', 'id', 'lang', 'style', 'title', 'accesskey', 'tabindex', 'onclick', 'controlsList', 'preload'] as $key) {
            if (!empty($options[$key])) {
                $attributes[] = $key . '="' . htmlspecialchars((string)$options[$key]) . '"';
            }
        }

        $posterImage = $this->getPosterImage($file);
        if ($posterImage instanceof \TYPO3\CMS\Core\Resource\ProcessedFile) {
            $attributes[] = 'poster="' . $posterImage->getPublicUrl() . '"';
        }

        // Clean up duplicate attributes
        $attributes = array_unique($attributes);

        $source = htmlspecialchars($this->getSource($file, $usedPathsRelativeToCurrentScript));

        $noVideoSupport = sprintf('<p>%s <a href="%s">%s</a></p>',
            self::translate('LLL:EXT:video_vtt/Resources/Private/Language/locallang.xlf:no_video_support'),
            $source,
            self::translate('LLL:EXT:video_vtt/Resources/Private/Language/locallang.xlf:video_download'),
        );

        return sprintf(
            '<video%s><source src="%s" type="%s">%s%s</video>',
            $attributes !== [] ? ' ' . implode(' ', $attributes) : '',
            $source,
            $file->getMimeType(),
            $this->getTracks($file),
            $noVideoSupport
        );
    }

    /**
     * The MimeTypes are used in a DisplayCondition
     */
    public function getPossibleMimeTypes(): array
    {
        return $this->possibleMimeTypes;
    }

    protected function getSource(FileInterface $file, bool $usedPathsRelativeToCurrentScript): string
    {
        $source = (string)$file->getPublicUrl();

        // We need an absolute path for the backend
        if (($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface
            && \TYPO3\CMS\Core\Http\ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()) {
            return PathUtility::getAbsoluteWebPath($source);
        }

        return $source;
    }

    protected function getTracks(FileInterface $file): string
    {
        $tracks = '';
        $originalFile = $file;
        if ($file instanceof FileReference) {
            $originalFile = $file->getOriginalFile();
        }

        if ($originalFile->getProperty('tracks') && ($originalFile->getMetaData()['uid'] ?? false)) {
            $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
            $relatedFiles = $fileRepository->findByRelation(
                'sys_file_metadata',
                'tracks',
                $originalFile->getMetaData()['uid']
            );

            foreach ($relatedFiles ?? [] as $fileObject) {
                $trackLanguage = $fileObject->getProperty('track_language') ?? '';
                $trackType = $fileObject->getProperty('track_type') ?? 'subtitles';
                $trackLabel = $fileObject->getProperty('track_label') ?? '';
                $trackDefault = $fileObject->getProperty('track_default') ?? false;
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

    protected function canRenderTrack(string $publicUrl, string $trackType, string $trackLanguage): bool
    {
        if ($publicUrl === '' || $publicUrl === '0') {
            return false;
        }
        //dont render subtitles without a track language
        return !($trackType === 'subtitles' && ($trackLanguage === '' || $trackLanguage === '0'));
    }

    protected function getPosterImage(FileInterface $file): ?ProcessedFile
    {
        $posterImage = null;

        if ($file->hasProperty('poster')) {
            $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
            $posterImage = $fileRepository->findByRelation('sys_file_reference', 'poster', $file->getUid());

            //if no poster in file reference, check metadata
            if (empty($posterImage)) {
                if ($file instanceof File) {
                    $metaData = $file->getMetaData()->get();
                } elseif ($file instanceof FileReference) {
                    $metaData = $file->getOriginalFile()->getMetaData()->get();
                } else {
                    $metaData = [];
                }
                $metaDataUid = $metaData['uid'] ?? 0;
                if ($metaDataUid > 0) {
                    $posterImage = $fileRepository->findByRelation('sys_file_metadata', 'poster', $metaDataUid);
                }
            }

            if (($posterImage[0] ?? null) instanceof \TYPO3\CMS\Core\Resource\FileReference) {
                $posterImage = $this->getCropVariant($posterImage[0]);
            }
        }

        return empty($posterImage) ? null : $posterImage;
    }

    /**
     * @param        $file
     */
    protected function getCropVariant($file, string $cropVariant = 'default'): ProcessedFile
    {
        $cropString = $file->getProperty('crop');
        $cropVariantCollection = CropVariantCollection::create($cropString);
        $cropArea = $cropVariantCollection->getCropArea($cropVariant); // cropVariant
        $processingInstructions = [
            'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($file),
        ];

        $imageService = GeneralUtility::makeInstance(ImageService::class);

        return $imageService->applyProcessingInstructions(
            $file,
            $processingInstructions
        );
    }

    private static function translate(string $lll): string
    {
        $languageServiceFactory = GeneralUtility::makeInstance(
            LanguageServiceFactory::class
        );
        // As we are in a static context we cannot get the current request in
        // another way this usually points to general flaws in your software-design
        $request = $GLOBALS['TYPO3_REQUEST'];
        $languageService = $languageServiceFactory->createFromSiteLanguage(
            $request->getAttribute('language')
            ?? $request->getAttribute('site')->getDefaultLanguage()
        );
        return $languageService->sL($lll);
    }
}
