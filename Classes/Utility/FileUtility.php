<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FileUtility
 * @package Digitalwerk\Typo3ElementRegistryCli\Utility
 */
class FileUtility
{
    /**
     * @param string $path
     * @param string $content
     */
    public static function createFile(string $path, string $content)
    {
        $path = GeneralUtility::getFileAbsFileName($path);

        $dir = explode('/', $path);
        array_pop($dir);
        $dir = implode('/', $dir);

        if (!is_dir($dir)) {
            mkdir($dir);
        }

        file_put_contents(
            $path,
            $content
        );
    }
}
