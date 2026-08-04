<?php
declare(strict_types=1);

namespace TRAW\VideoVtt\Utility;

use TRAW\VideoVtt\Events\PosterImageCropVariantEvent;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

class PosterImageUtility
{
    public function getPosterImage(FileInterface $file, string $cropVariant = 'default', bool $process = true): ProcessedFile|FileReference|null
    {
        $posterImage = null;

        if ($file->hasProperty('poster')) {
            $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
            $posterImage = $fileRepository->findByRelation('sys_file_reference', 'poster', $file->getProperty('uid'));

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

            if (($posterImage[0] ?? null) instanceof FileReference) {
                if ($process) {
                    $posterImage = $this->getCropVariant($posterImage[0], $cropVariant);
                } else {
                    $posterImage = $posterImage[0];
                }
            }
        }

        return empty($posterImage) ? null : $posterImage;
    }

    public function getPosterImageTag(FileInterface $file, string $cropVariant = 'default'): string
    {
        $posterImage = $this->getPosterImage($file, $cropVariant, false);
        if ($posterImage instanceof FileReference) {
            $processedImage = $this->getCropVariant($posterImage);

            $tag = new TagBuilder('img');
            $tag->addAttribute('class', 'audio-poster');
            $tag->addAttribute('src', $processedImage->getPublicUrl());
            $tag->addAttribute('width', $processedImage->getProperty('width'));
            $tag->addAttribute('height', $processedImage->getProperty('height'));

            return $tag->render();
        }

        return '';
    }

    public function getCropVariant(FileReference $fileReference, string $cropVariant = 'default'): ProcessedFile
    {
        $cropVariant = GeneralUtility::makeInstance(EventDispatcher::class)->dispatch(
            new PosterImageCropVariantEvent($cropVariant, PosterImageUtility::class)
        )->getCropVariant();

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
