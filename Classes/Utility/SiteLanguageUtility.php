<?php

namespace TRAW\VideoVtt\Utility;

use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SiteLanguageUtility
{
    /**
     * @var array
     */
    protected static $languages = [];

    /**
     * @param array $configuration
     *
     * @return void
     */
    public static function getIsoTwoLetterCodes(array &$configuration)
    {
        if (count(self::$languages)) {
            $configuration['items'] = array_merge($configuration['items'], self::$languages);
            return;
        }
        $site = GeneralUtility::makeInstance(SiteFinder::class)
            ->getSiteByPageId($configuration['row']['pid']);
        foreach ($site->getAllLanguages() as $language) {
            if (!in_array($language->getLocale()->getLanguageCode(), array_column(self::$languages, 1)))
                self::$languages[] = ['label' => $language->getNavigationTitle(), 'value' => $language->getLocale()->getLanguageCode()];
        }

        $configuration['items'] = array_merge($configuration['items'], self::$languages);
    }
}
