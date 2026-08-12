<?php

namespace TRAW\VideoVtt\Resource\Rendering;

use Psr\Http\Message\ServerRequestInterface;
use TRAW\VideoVtt\Options\Options;
use TRAW\VideoVtt\Utility\AttributeUtility;
use TRAW\VideoVtt\Utility\FileUtility;
use TRAW\VideoVtt\Utility\PosterImageUtility;
use TRAW\VideoVtt\Utility\TracksUtility;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Resource\Rendering\FileRendererInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

final class VideoTagRenderer implements FileRendererInterface
{
    /**
     * Mime types that can be used in the HTML Video tag
     */
    protected array $possibleMimeTypes = ['video/mp4', 'video/webm', 'video/ogg', 'video/x-m4v', 'application/ogg'];


    public function getPriority(): int
    {
        return 7;
    }

    /**
     * Check if given File(Reference) can be rendered
     *
     * @param FileInterface $file File or FileReference to render
     *
     * @return bool
     */
    public function canRender(FileInterface $file)
    {
        return in_array($file->getMimeType(), $this->possibleMimeTypes, true);
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
    public function render(FileInterface $file, $width, $height, array $options = []): string
    {
        if (($options['returnUrl'] ?? false) === true) {
            return htmlspecialchars(GeneralUtility::makeInstance(FileUtility::class)->getAbsoluteUrl($file->getPublicUrl()), ENT_QUOTES | ENT_HTML5);
        }

        $attributeUtility = new AttributeUtility();
        $attributes = $attributeUtility->getVideoAttributes($file, (int)$width, (int)$height, $options);
        $sourceTime = $attributeUtility->getSourceTime($file, $options);

        $posterImageUtility = new PosterImageUtility();
        $posterImage = $posterImageUtility->getPosterImage($file);
        if ($posterImage instanceof \TYPO3\CMS\Core\Resource\ProcessedFile) {
            $attributes[] = 'poster="' . $posterImage->getPublicUrl() . '"';
        }

        $tracksUtility = new TracksUtility();
        $tracks = $tracksUtility->getTracks($file);

        $src = htmlspecialchars($this->getSource($file));
        $noVideoSupport = sprintf('<p>%s <a href="%s">%s</a></p>',
            self::translate('LLL:EXT:video_vtt/Resources/Private/Language/locallang.xlf:no_video_support'),
            $src,
            self::translate('LLL:EXT:video_vtt/Resources/Private/Language/locallang.xlf:video_download'),
        );

        return sprintf(
            '<video%s><source src="%s%s" type="%s">%s%s</video>',
            $attributes !== [] ? ' ' . implode(' ', $attributes) : '',
            $src,
            $sourceTime,
            $file->getMimeType(),
            $tracks,
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
