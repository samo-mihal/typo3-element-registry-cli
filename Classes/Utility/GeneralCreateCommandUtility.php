<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Utility;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\ElementSetup;

/**
 * Class GeneralCreateCommandUtility
 * @package Digitalwerk\Typo3ElementRegistryCli\Utility
 */
class GeneralCreateCommandUtility
{
    /**
     * @param string $extensionName
     * @param string $elementType
     * @return array|false
     */
    public static function getExistingElementsInExtension(string $extensionName, string $elementType)
    {
        $result = [];
        if ($elementType === ElementSetup::CONTENT_ELEMENT) {
            $pathToDirectories = 'public/typo3conf/ext/' . $extensionName . '/Classes/ContentElement';
            $files = array_diff(scandir($pathToDirectories), ['.', '..']);

            foreach ($files as $file) {
                $fileLines = file('public/typo3conf/ext/' . $extensionName . '/Classes/ContentElement/' . $file);
                $fileLines = array_map('trim', $fileLines);
                foreach ($fileLines as $fileLine) {
                    if (strpos($fileLine, '<?php') !== false) {
                        $result[] = $file;
                        break;
                    }
                }
            }
        } elseif ($elementType === ElementSetup::PAGE_TYPE) {
            $pathToDirectories = 'public/typo3conf/ext/' . $extensionName . '/Classes/Domain/Model';
            $files = array_diff(scandir($pathToDirectories), ['.', '..']);

            foreach ($files as $file) {
                $fileLines = file('public/typo3conf/ext/' . $extensionName . '/Classes/Domain/Model/' . $file);
                $fileLines = array_map('trim', $fileLines);
                foreach ($fileLines as $fileLine) {
                    if (strpos($fileLine, 'protected static $doktype') !== false &&
                        strpos($fileLine, 'protected static $doktype = 0;') === false
                    ) {
                        $result[] = $file;
                        break;
                    }
                }
            }
        } elseif ($elementType === ElementSetup::RECORD) {
            $pathToDirectories = 'public/typo3conf/ext/' . $extensionName . '/Configuration/TCA';
            $files = array_diff(scandir($pathToDirectories), ['.', '..']);

            foreach ($files as $file) {
                $file = explode('_', $file);
                $file = ucfirst(end($file));
                if (strpos($file, '.php')) {
                    $result[] = $file;
                }
            }
        } elseif ($elementType === ElementSetup::PLUGIN) {
            $extensionName = str_replace(' ', '', ucwords(str_replace('_', ' ', $extensionName)));
            $result = array_keys(
                $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions'][$extensionName]['plugins']
            );
        }

        return $result;
    }

    /**
     * @return array|false
     */
    public static function getExtensions()
    {
        $pathToDirectories = 'public/typo3conf/ext/';
        return array_diff(scandir($pathToDirectories), ['.', '..']);
    }
}
