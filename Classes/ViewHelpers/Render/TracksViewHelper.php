<?php
declare(strict_types=1);

namespace TRAW\VideoVtt\ViewHelpers\Render;

use TRAW\VideoVtt\Utility\TracksUtility;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class TracksViewHelper extends AbstractViewHelper
{
    public function __construct(
        private readonly TracksUtility $tracksUtility
    )
    {
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('file', FileReference::class, 'File to check', true);
        $this->registerArgument('filter', 'string', 'Filter types');
        $this->registerArgument('filter-value', 'string', 'Filter value');
    }

    public function render(): string
    {
        $file = $this->arguments['file'];

        $filter = $this->arguments['filter'] ?? false;
        $filterValue = $this->arguments['filter-value'] ?? false;

        if ($filterValue === 'default') {
            return $this->tracksUtility->getDefaultTrack($file);
        } elseif ($filterValue) {
            if ($filter === 'type' || $filter === 'kind') {
                return $this->tracksUtility->getTrackByType($file, $filterValue);
            } elseif ($filter === 'language' || $filter === 'srclang') {
                return $this->tracksUtility->getTrackByLanguage($file, $filterValue);
            } else {
                throw new \Exception('Invalid filter');
            }
        } else {
            return $this->tracksUtility->getTracks($file);
        }
    }
}
