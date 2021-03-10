<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use function Symfony\Component\String\u;

/**
 * Class ControllerUtility
 * @package Digitalwerk\Typo3ElementRegistryCli\Utility
 */
class ControllerUtility
{
    /**
     * @param string $path
     * @param string $actionName
     */
    public static function createAction(string $path, string $actionName)
    {
        $path = GeneralUtility::getFileAbsFileName($path);
        $originalContent = file_get_contents($path);

        $actionTemplate = file_get_contents(
            GeneralUtility::getFileAbsFileName(
                'EXT:typo3_element_registry_cli/Resources/Private/Templates/Plugin/Action.txt'
            )
        );
        $actionTemplate = str_replace([
            '{actionName}', '{actionNameUpperFirst}'
        ], [
            $actionName,
            ucfirst($actionName)
        ], $actionTemplate);
        $before = u($originalContent)->before("{", true);
        $after = $before . "\n\n" . u($actionTemplate)->trimEnd();
        $originalContent = u($originalContent)->replace($before, $after);

        file_put_contents($path, $originalContent);
    }
}
