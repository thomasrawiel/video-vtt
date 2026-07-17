<?php
declare(strict_types=1);

namespace TRAW\VideoVtt\ViewHelpers\Render;


use TRAW\VideoVtt\Utility\PosterImageUtility;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
use TYPO3Fluid\Fluid\Core\ViewHelper\InvalidArgumentValueException;

final class PosterViewHelper extends AbstractTagBasedViewHelper
{
    protected $tagName = 'img';

    public function __construct(
        private readonly PosterImageUtility $posterImageUtility,
        private readonly ImageService       $imageService
    )
    {
        parent::__construct();
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('file', FileReference::class, 'File to check', true);

        $this->registerArgument('crop', 'string|bool|array', 'overrule cropping of image (setting to FALSE disables the cropping set in FileReference)');
        $this->registerArgument('cropVariant', 'string', 'select a cropping variant, in case multiple croppings have been specified or stored in FileReference', false, 'default');
        $this->registerArgument('fileExtension', 'string', 'Custom file extension to use');

        $this->registerArgument('width', 'string', 'width of the image. This can be a numeric value representing the fixed width of the image in pixels. But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width in the TypoScript Reference on https://docs.typo3.org/permalink/t3tsref:confval-imgresource-width for possible options.');
        $this->registerArgument('height', 'string', 'height of the image. This can be a numeric value representing the fixed height of the image in pixels. But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.height in the TypoScript Reference https://docs.typo3.org/permalink/t3tsref:confval-imgresource-height for possible options.');
        $this->registerArgument('minWidth', 'int', 'minimum width of the image');
        $this->registerArgument('minHeight', 'int', 'minimum height of the image');
        $this->registerArgument('maxWidth', 'int', 'maximum width of the image');
        $this->registerArgument('maxHeight', 'int', 'maximum height of the image');
        $this->registerArgument('absolute', 'bool', 'Force absolute URL', false, false);
        $this->registerArgument('base64', 'bool', 'Adds the image data base64-encoded inline to the image‘s "src" attribute. Useful for FluidEmail templates.', false, false);
    }

    public function render(): string
    {
        $file = $this->arguments['file'];
        $image = $this->posterImageUtility->getPosterImage($file, 'default', false);

        if ($image === null) {
            return '';
        }

        //adapted from Fluid
        if ((string)$this->arguments['fileExtension'] && !GeneralUtility::inList($GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'], (string)$this->arguments['fileExtension'])) {
            throw new InvalidArgumentValueException(
                $this->getExceptionMessage(
                    'The extension ' . $this->arguments['fileExtension'] . ' is not specified in $GLOBALS[\'TYPO3_CONF_VARS\'][\'GFX\'][\'imagefile_ext\']'
                    . ' as a valid image file extension and can not be processed.',
                ),
                1618989190
            );
        }

        try {

            $cropString = $this->arguments['crop'];
            if ($cropString === null && $image->hasProperty('crop') && $image->getProperty('crop')) {
                $cropString = $image->getProperty('crop');
            }

            // CropVariantCollection needs a string, but this VH could also receive an array
            if (is_array($cropString)) {
                $cropString = json_encode($cropString);
            }

            $cropVariantCollection = CropVariantCollection::create((string)$cropString);
            $cropVariant = $this->arguments['cropVariant'] ? : 'default';
            $cropArea = $cropVariantCollection->getCropArea($cropVariant);
            $processingInstructions = [
                'width' => $this->arguments['width'],
                'height' => $this->arguments['height'],
                'minWidth' => $this->arguments['minWidth'],
                'minHeight' => $this->arguments['minHeight'],
                'maxWidth' => $this->arguments['maxWidth'],
                'maxHeight' => $this->arguments['maxHeight'],
                'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($image),
            ];
            if (!empty($this->arguments['fileExtension'] ?? '')) {
                $processingInstructions['fileExtension'] = $this->arguments['fileExtension'];
            }
            $processedImage = $this->imageService->applyProcessingInstructions($image, $processingInstructions);

            if ($this->arguments['base64']) {
                $imageSrc = 'data:' . $processedImage->getMimeType() . ';base64,' . base64_encode($processedImage->getContents());
            } else {
                $imageSrc = $this->imageService->getImageUri($processedImage, $this->arguments['absolute']);
            }

            if (!$this->tag->hasAttribute('data-focus-area')) {
                $focusArea = $cropVariantCollection->getFocusArea($cropVariant);
                if (!$focusArea->isEmpty()) {
                    $this->tag->addAttribute('data-focus-area', (string)$focusArea->makeAbsoluteBasedOnFile($image));
                }
            }
            $this->tag->addAttribute('src', $imageSrc);
            $this->tag->addAttribute('width', $processedImage->getProperty('width'));
            $this->tag->addAttribute('height', $processedImage->getProperty('height'));

            if (isset($this->additionalArguments['alt']) && $this->additionalArguments['alt'] === '') {
                // In case the "alt" attribute is explicitly set to an empty string, respect
                // this to allow excluding it from screen readers, improving accessibility.
                $this->tag->addAttribute('alt', '');
            } elseif (!isset($this->additionalArguments['alt'])) {
                // The alt-attribute is mandatory to have valid html-code, therefore use "alternative" property or empty
                $this->tag->addAttribute('alt', $image->getProperty('alternative') ?? '');
            }
            // Only add title-attribute from image if not set in additional-arguments.
            // In case the "title" attribute is explicitly set to an empty string,
            // it will not fallback to an image-title.
            // This allows excluding it explicitly from screen readers, improving accessibility.
            if (!isset($this->additionalArguments['title'])) {
                $title = trim((string)($image->hasProperty('title') ? $image->getProperty('title') : ''));
                // The title-attribute is not mandatory, therefore use "title" property or omit fully
                if ($title !== '') {
                    $this->tag->addAttribute('title', $title);
                }
            }
        } catch (ResourceDoesNotExistException $e) {
            // thrown if file does not exist
            throw new Exception($this->getExceptionMessage($e->getMessage()), 1509741911, $e);
        } catch (\UnexpectedValueException $e) {
            // thrown if a file has been replaced with a folder
            throw new Exception($this->getExceptionMessage($e->getMessage()), 1509741912, $e);
        } catch (\InvalidArgumentException $e) {
            // thrown if file storage does not exist
            throw new Exception($this->getExceptionMessage($e->getMessage()), 1509741914, $e);
        }
        return $this->tag->render();


    }
}
