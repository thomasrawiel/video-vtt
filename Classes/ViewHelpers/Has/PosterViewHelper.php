<?php
declare(strict_types=1);

namespace TRAW\VideoVtt\ViewHelpers\Has;

use TRAW\VideoVtt\Utility\PosterImageUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

final class PosterViewHelper extends AbstractHasViewHelper
{
    public function __construct(private readonly PosterImageUtility $posterImageUtility)
    {
    }

    public static function verdict(array $arguments, RenderingContextInterface $renderingContext): bool
    {
        $file = $arguments['file'] ?? null;

        if ($file !== null) {
            $posterImageUtility = GeneralUtility::makeInstance(PosterImageUtility::class);
            $posterImage = $posterImageUtility->getPosterImage($file, 'default', false);

            return $posterImage !== null;
        }
        
        return false;
    }
}
