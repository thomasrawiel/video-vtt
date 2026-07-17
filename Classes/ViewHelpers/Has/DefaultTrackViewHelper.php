<?php
declare(strict_types=1);

namespace TRAW\VideoVtt\ViewHelpers\Has;

use TRAW\VideoVtt\Utility\TracksUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

final class DefaultTrackViewHelper extends AbstractHasViewHelper
{
    public static function verdict(array $arguments, RenderingContextInterface $renderingContext): bool
    {
        $file = $arguments['file'] ?? null;

        if ($file !== null) {
            $tracksUtility = GeneralUtility::makeInstance(TracksUtility::class);
            $tracks = array_values(array_filter(
                $tracksUtility->getTracksArray($file),
                static fn(array $track): bool => (bool)($track['track_default'] ?? false)
            ));

            return $tracks !== [];
        }

        return false;
    }
}
