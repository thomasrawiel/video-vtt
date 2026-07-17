<?php
declare(strict_types=1);

namespace TRAW\VideoVtt\EventListener;

use FriendsOfTYPO3\Headless\Event\EnrichFileDataEvent;
use FriendsOfTYPO3\Headless\Utility\FileUtility;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use TRAW\VideoVtt\Events\PosterImageCropVariantEvent;
use TRAW\VideoVtt\Options\Options;
use TRAW\VideoVtt\Utility\AttributeUtility;
use TRAW\VideoVtt\Utility\PosterImageUtility;
use TRAW\VideoVtt\Utility\TracksUtility;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class EnrichFileDataEventListener
{
    public function __construct(
        private readonly PosterImageUtility $posterImageUtility,
        private readonly TracksUtility      $tracksUtility,
        private readonly FileUtility        $fileUtility)
    {
    }

    public function __invoke(EnrichFileDataEvent $event)
    {
        $file = $event->getOriginal();
        $properties = $event->getProperties();

        if ($properties['type'] === 'video' || $properties['type'] === 'audio') {
            $options = new Options($file);

            foreach ($options->toArray() as $key => $value) {
                if (!isset($properties[$key])) {
                    $properties[$key] = $value;
                    if ($key === 'controlsList') {
                        $properties['controlsListValue'] = $options->getControlsListValue();
                    }
                }
            }
            $properties['poster'] = $this->getPoster($file);
            $properties['tracks'] = $this->getTracks($file);
        }

        $event->setProperties($properties);
    }

    private function getPoster(FileInterface $file, string $cropVariant = 'default'): array|null
    {
        $cropVariant = GeneralUtility::makeInstance(EventDispatcher::class)->dispatch(
            new PosterImageCropVariantEvent($cropVariant, EnrichFileDataEventListener::class)
        )->getCropVariant();

        $posterImage = $this->posterImageUtility->getPosterImage($file, $cropVariant, false);
        if ($posterImage === null) {
            return null;
        }

        return $this->fileUtility->processFile($posterImage, [], $cropVariant);
    }

    private function getTracks(FileInterface $file): array
    {
        $tracks = $this->tracksUtility->getTracksArray($file);
        
        foreach($tracks as $key => $track) {
            $tracks[$key]['public_url'] = $this->fileUtility->getAbsoluteUrl($track['public_url']);
        }
        
        return $tracks;
    }
}
