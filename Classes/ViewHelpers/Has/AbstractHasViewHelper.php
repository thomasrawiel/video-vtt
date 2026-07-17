<?php
declare(strict_types=1);

namespace TRAW\VideoVtt\ViewHelpers\Has;

use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

abstract class AbstractHasViewHelper extends AbstractConditionViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('file', FileReference::class, 'File to check', true);
    }

    public static function verdict(array $arguments, RenderingContextInterface $renderingContext): bool
    {
        return parent::verdict($arguments, $renderingContext);
    }
}
