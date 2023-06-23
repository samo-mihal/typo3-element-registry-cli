<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Utility;

use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ControllerUtility
 * @package Digitalwerk\Typo3ElementRegistryCli\Utility
 */
class ExtensionUtility
{
    /**
     * @var PackageManager
     */
    protected static $packagerManager = null;

    /**
     * ExtensionUtility init.
     */
    public static function init()
    {
        // @extensionScannerIgnoreLine
        self::$packagerManager = GeneralUtility::makeInstance(PackageManager::class);
    }

    /**
     * @return array
     */
    public static function getActiveExtensions()
    {
        self::init();
        return array_keys(self::$packagerManager->getActivePackages());
    }
}
