<?php
declare(strict_types=1);

namespace TRAW\VideoVtt\Utility;

use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * from ext:headless
 */
class FileUtility
{
    public function __construct(
        private readonly ContentObjectRenderer $contentObjectRenderer,
    )
    {
    }

    public function getAbsoluteUrl(string $fileUrl): string
    {
        $siteUrl = $this->getNormalizedParams()->getSiteUrl();
        $sitePath = str_replace($this->getNormalizedParams()->getRequestHost(), '', $siteUrl);
        $absoluteUrl = trim($fileUrl);
        if (stripos($absoluteUrl, 'http') !== 0) {
            $fileUrl = preg_replace('#^' . preg_quote($sitePath, '#') . '#', '', $fileUrl);
            $fileUrl = $siteUrl . $fileUrl;
        }

        return $fileUrl;
    }

    protected function getNormalizedParams(): NormalizedParams
    {
        return $this->contentObjectRenderer->getRequest()->getAttribute('normalizedParams');
    }
}
