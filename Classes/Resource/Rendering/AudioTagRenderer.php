<?php
declare(strict_types=1);

namespace TRAW\VideoVtt\Resource\Rendering;

use Psr\Http\Message\ServerRequestInterface;
use TRAW\VideoVtt\Options\Options;
use TRAW\VideoVtt\Utility\AttributeUtility;
use TRAW\VideoVtt\Utility\PosterImageUtility;
use TRAW\VideoVtt\Utility\FileUtility;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Resource\Rendering\FileRendererInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Service\ImageService;

class AudioTagRenderer implements FileRendererInterface
{
    /**
     * Mime types that can be used in the HTML Video tag
     *
     * @var array
     */
    protected $possibleMimeTypes = ['audio/mpeg', 'audio/wav', 'audio/x-wav', 'audio/ogg'];

    protected array $excludeAttributes = ['api', 'no-cookie'];

    public function __construct(
        private readonly PosterImageUtility $posterImageUtility,
        private readonly AttributeUtility   $attributeUtility,
    )
    {
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

    public function getPriority(): int
    {
        return 7;
    }

    public function render(FileInterface $file, $width, $height, array $options = [])
    {
        $attributes = $this->attributeUtility->getAudioAttributes($file, $options);

        $imageTag = $this->posterImageUtility->getPosterImageTag($file);

        $src = htmlspecialchars($this->getSource($file));
        $sourceTime = $this->attributeUtility->getSourceTime($file, $options);

        return $imageTag
            . sprintf(
                '<audio%s><source src="%s%s" type="%s"></audio>',
                empty($attributes) ? '' : ' ' . implode(' ', $attributes),
                $src,
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
