<?php
declare(strict_types=1);

namespace TRAW\VideoVtt\ViewHelpers\Has;

use TRAW\VideoVtt\Utility\TracksUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

final class TracksViewHelper extends AbstractHasViewHelper
{
    public static function verdict(array $arguments, RenderingContextInterface $renderingContext): bool
    {
        $file = $arguments['file'] ?? null;

        if ($file !== null) {
            $tracksUtility = GeneralUtility::makeInstance(TracksUtility::class);
            $tracks = $tracksUtility->getTracksArray($file);

            return $tracks !== [];
        }

        return false;
    }
}
