<?php

/*
 * Copyright notice
 *
 * (c) 2023 Thomas Rawiel <t.rawiel@lingner.com>
 *
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 *
 * Last modified: 27.10.23, 10:09
 */

namespace TRAW\VideoVtt\Utility;

/**
 * Class CoreUtility
 */
class CoreUtility
{
    public static function getTypo3Version(): string
    {
        return (new \TYPO3\CMS\Core\Information\Typo3Version())->getVersion();
    }

    public static function getTypo3MajorVersion(): int
    {
        return (new \TYPO3\CMS\Core\Information\Typo3Version())->getMajorVersion();
    }

    public static function isFrontend(): bool
    {
        \TYPO3\CMS\Core\Http\ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend();
    }

    public static function isBackend(): bool
    {
        return \TYPO3\CMS\Core\Http\ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend();
    }
}
