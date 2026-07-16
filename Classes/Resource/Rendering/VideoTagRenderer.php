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

use FriendsOfTYPO3\Headless\Utility\FileUtility;
use Psr\Http\Message\ServerRequestInterface;
use TRAW\VideoVtt\Options\Options;
use TRAW\VideoVtt\Utility\CoreUtility;
use TRAW\VideoVtt\Utility\PosterImageUtility;
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
        if (($options['returnUrl'] ?? false) === true) {
            return htmlspecialchars(GeneralUtility::makeInstance(FileUtility::class)->getAbsoluteUrl($file->getPublicUrl()), ENT_QUOTES | ENT_HTML5);
        }

        $options = new Options($file, $options);

        $attributes = [];
        if ($options->getAdditionalAttributes() !== []) {
            $attributes[] = GeneralUtility::implodeAttributes($options->getAdditionalAttributes(), true, true);
        }

        if ($options->getData() !== []) {
            $data = $options->getData();
            array_walk($data, function (&$value, $key): void {
                $value = 'data-' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
            });
            $attributes[] = implode(' ', $data);
        }

        if ((int)$width > 0) {
            $attributes[] = 'width="' . (int)$width . '"';
        }

        if ((int)$height > 0) {
            $attributes[] = 'height="' . (int)$height . '"';
        }

        if ($options->getControls()) {
            $attributes[] = 'controls';
        }

        if (!$options->getPicinpic()) {
            $attributes[] = 'disablePictureInPicture';
        }

        if ($options->getAutoPlay()) {
            $attributes[] = 'autoplay';
            $attributes[] = 'playsinline';
            $attributes[] = 'muted';
        }

        if ($options->getMute()) {
            $attributes[] = 'muted';
        }

        if ($options->getLoop()) {
            $attributes[] = 'loop';
        }

        if ($options->getAdditionalConfig() !== []) {
            foreach ($options->getAdditionalConfig() as $key => $value) {
                if ((bool)$value) {
                    $attributes[] = htmlspecialchars((string)$key);
                }
            }
        }

        foreach (['class', 'dir', 'id', 'lang', 'style', 'title', 'accesskey', 'tabindex', 'onclick', 'preload'] as $key) {
            if ($options->get($key)) {
                $attributes[] = $key . '="' . htmlspecialchars((string)$options->get($key)) . '"';
            }
        }

        if ($options->getControlsList()) {
            $controlsList = $options->getControlsListValueVideo();
            $attributes[] = 'controlsList="' . htmlspecialchars((string)$controlsList) . '"';
        }

        $posterImage = PosterImageUtility::getPosterImage($file);
        if ($posterImage instanceof \TYPO3\CMS\Core\Resource\ProcessedFile) {
            $attributes[] = 'poster="' . $posterImage->getPublicUrl() . '"';
        }

        // Clean up duplicate attributes
        $attributes = array_unique($attributes);

        $source = htmlspecialchars($this->getSource($file, $usedPathsRelativeToCurrentScript));

        $start = $options->getStartTime();
        if ($start < 0) {
            $start = 0;
        }
        $sourceParams = [$start];

        $end = $options->getEndTime();
        if ($end > $start) {
            $sourceParams[] = $end;
        }

        $sourceTime = '';
        if ($start !== 0 || $end !== 0) {
            $sourceTime = sprintf('#t=%s', implode(',', $sourceParams));
        }

        $noVideoSupport = sprintf('<p>%s <a href="%s">%s</a></p>',
            self::translate('LLL:EXT:video_vtt/Resources/Private/Language/locallang.xlf:no_video_support'),
            $source,
            self::translate('LLL:EXT:video_vtt/Resources/Private/Language/locallang.xlf:video_download'),
        );

        return sprintf(
            '<video%s><source src="%s%s" type="%s">%s%s</video>',
            $attributes !== [] ? ' ' . implode(' ', $attributes) : '',
            $source,
            $sourceTime,
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
        /** @var File $originalFile */
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

            foreach ($relatedFiles as $fileObject) {
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
