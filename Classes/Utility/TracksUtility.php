<?php
declare(strict_types=1);

namespace TRAW\VideoVtt\Utility;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

class TracksUtility
{
    public function getTracks(FileInterface $file): string
    {
        $tracks = '';

        foreach ($this->getTracksArray($file) as $track) {
            $tracks .= $this->renderTrackTag(
                $track['public_url'],
                $track['track_type'],
                $track['track_language'],
                $track['track_label'],
                $track['track_default']
            );
        }

        return $tracks;
    }

    public function getDefaultTrack(FileInterface $file): string
    {
        $tracks = '';
        foreach ($this->getTracksArray($file) as $track) {
            if ((bool)($track['track_default'])) {
                $tracks .= $this->renderTrackTag(
                    $track['public_url'],
                    $track['track_type'],
                    $track['track_language'],
                    $track['track_label'],
                    $track['track_default']
                );
            }
        }

        return $tracks;
    }

    public function getTrackByType(FileInterface $file, string $trackType): string
    {
        $tracks = '';

        foreach ($this->getTracksArray($file) as $track) {
            if ($track['track_type'] === $trackType) {
                $tracks .= $this->renderTrackTag(
                    $track['public_url'],
                    $track['track_type'],
                    $track['track_language'],
                    $track['track_label'],
                    $track['track_default']
                );
            }
        }

        return $tracks;
    }

    public function getTrackByLanguage(FileInterface $file, string $trackLanguage): string
    {
        $tracks = '';

        foreach ($this->getTracksArray($file) as $track) {
            if ($track['track_language'] === $trackLanguage) {
                $tracks .= $this->renderTrackTag(
                    $track['public_url'],
                    $track['track_type'],
                    $track['track_language'],
                    $track['track_label'],
                    $track['track_default']
                );
            }
        }

        return $tracks;
    }

    public function getTracksArray(FileInterface $file): array
    {
        $tracksArray = [];
        $originalFile = $file instanceof FileReference ? $file->getOriginalFile() : $file;

        $relatedFiles = $this->getTrackFiles($originalFile);

        if ($relatedFiles === []) {
            return [];
        }

        foreach ($relatedFiles as $track) {
            $trackLanguage = $track->getProperty('track_language') ?? '';
            $trackType = $track->getProperty('track_type') ?? 'subtitles';
            $trackLabel = $track->getProperty('track_label') ?? '';
            $trackDefault = $track->getProperty('track_default') ?? false;
            $publicUrl = (string)$track->getPublicUrl();

            if ($this->canRenderTrack($publicUrl, $trackType, $trackLanguage)) {
                $tracksArray[] = [
                    'public_url' => $publicUrl,
                    'track_type' => $trackType,
                    'track_language' => $trackLanguage,
                    'track_label' => $trackLabel,
                    'track_default' => $trackDefault,
                ];
            }
        }

        return $tracksArray;
    }

    protected function getTrackFiles(FileInterface $file): array
    {
        if ($file->getProperty('tracks') && ($file->getMetaData()['uid'] ?? false)) {
            $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
            $relatedFiles = $fileRepository->findByRelation(
                'sys_file_metadata',
                'tracks',
                $file->getMetaData()['uid'] ?? 0
            );

            return $relatedFiles;
        }
        return [];
    }

    protected function renderTrackTag(string $publicUrl, string $trackType, string $trackLanguage, string $trackLabel, int|bool $trackDefault): string
    {
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

        return $trackTag->render() . PHP_EOL;
    }

    protected function canRenderTrack(string $publicUrl, string $trackType, string $trackLanguage): bool
    {
        if ($publicUrl === '' || $publicUrl === '0') {
            return false;
        }
        //dont render subtitles without a track language
        return !($trackType === 'subtitles' && ($trackLanguage === '' || $trackLanguage === '0'));
    }
}
