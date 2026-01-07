<?php
declare(strict_types=1);

namespace TRAW\VideoVtt\Utility;

use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\ImageService;

class PosterImageUtility
{
    public static function getPosterImage(FileInterface $file, bool $returnProcessedImage = true): ProcessedFile|FileReference|null
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
                $posterImage = $returnProcessedImage
                    ? self::getCropVariant($posterImage[0])
                    : $posterImage[0];
            }
        }

        return empty($posterImage) ? null : $posterImage;
    }

    public static function getCropVariant(FileReference $fileReference, string $cropVariant = 'default'): ProcessedFile
    {
        $cropString = $fileReference->getProperty('crop');
        $cropVariantCollection = CropVariantCollection::create($cropString);
        $cropArea = $cropVariantCollection->getCropArea($cropVariant); // cropVariant
        $processingInstructions = [
            'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($fileReference),
        ];

        $imageService = GeneralUtility::makeInstance(ImageService::class);

        return $imageService->applyProcessingInstructions(
            $fileReference,
            $processingInstructions
        );
    }
}
