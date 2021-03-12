<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class SQLUtility
 * @package Digitalwerk\Typo3ElementRegistryCli\Utility
 */
class SQLUtility
{
    /**
     * @param string $path
     * @param string $tableName
     */
    public static function createTable(string $path, string $tableName)
    {
        $path = GeneralUtility::getFileAbsFileName($path);
        $originalContent = file_get_contents($path);

        $content = file_get_contents(
            GeneralUtility::getFileAbsFileName(
                'EXT:typo3_element_registry_cli/Resources/Private/Templates/Utility/SQL/Table.txt'
            )
        );
        $content = str_replace(['{table}'], [$tableName], $content);

        $originalContent = $content . "\n". $originalContent;

        file_put_contents($path, $originalContent);
    }
}
