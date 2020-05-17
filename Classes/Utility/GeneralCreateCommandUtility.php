<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Utility;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\ElementSetup;
use InvalidArgumentException;

/**
 * Class GeneralCreateCommandUtility
 * @package Digitalwerk\Typo3ElementRegistryCli\Utility
 */
class GeneralCreateCommandUtility
{
    /**
     * @param array $array
     * @param string $key
     * @param array $new
     * @return array
     */
    public static function arrayInsertAfter( array $array, $key, array $new ) {
        $keys = array_keys( $array );
        $index = array_search( $key, $keys );
        $pos = false === $index ? count( $array ) : $index + 1;
        return array_merge( array_slice( $array, 0, $pos ), $new, array_slice( $array, $pos ) );
    }

    /**
     * @param string $filename
     * @param array $newLines
     * @param string $universalStringInFile
     * @param int $linesAfterSpecificString
     * @param array $onFail
     * @param array $onSecondFail
     * @return void if filename does not exist return false
     * if filename does not exist return false
     */
    public static function importStringInToFileAfterString(
        string $filename,
        array $newLines,
        string $universalStringInFile,
        int $linesAfterSpecificString,
        array $onFail = [],
        array $onSecondFail = []
    )
    {
        $lines = file($filename);
        $trimmedLines = array_map('trim', $lines);
        $numberOfMatchedLine = array_search($universalStringInFile, $trimmedLines);
        if (false !== $numberOfMatchedLine) {
            $lines = self::arrayInsertAfter($lines,$numberOfMatchedLine + $linesAfterSpecificString, $newLines);
            file_put_contents($filename, $lines);
        } elseif (!empty($onFail)) {
            $numberOfMatchedLine = array_search($onFail['universalStringInFile'], $trimmedLines);
            $lines = self::arrayInsertAfter($lines,$numberOfMatchedLine + $onFail['linesAfterSpecificString'], [$onFail['newLines']]);
            file_put_contents($filename, $lines);
        } elseif (!empty($onSecondFail)) {
            $numberOfMatchedLine = array_search($onSecondFail['universalStringInFile'], $trimmedLines);
            $lines = self::arrayInsertAfter($lines,$numberOfMatchedLine + $onSecondFail['linesAfterSpecificString'], [$onSecondFail['newLines']]);
            file_put_contents($filename, $lines);
        }
    }

    /**
     * @param string $filename
     * @param string $universalStringInFile
     * @param int $linesAfterString
     * @param string $afterString
     * @param int $positionAfterString
     * @param string $insertStr
     * @return void
     */
    public static function insertStringToFileInlineAfter(
        string $filename,
        string $universalStringInFile,
        int $linesAfterString,
        string $afterString,
        int $positionAfterString,
        string $insertStr)
    {
        if (file_exists($filename)) {
            $lines = file($filename);
            $trimmedLines = array_map('trim', $lines);
            $numberOfMatchedLine = array_search($universalStringInFile, $trimmedLines);
            $str = $lines[$numberOfMatchedLine + $linesAfterString];
            $pos = strpos($str, $afterString) + strlen($afterString) + $positionAfterString;
            $str = substr($str, 0, $pos) . $insertStr . substr($str, $pos);
            $str = str_replace(',\'', '\'', $str);
            $lines[$numberOfMatchedLine + $linesAfterString] = $str;
            file_put_contents($filename, $lines);
        } else {
            throw new InvalidArgumentException('File ' . $filename . ' does not exist');
        }
    }


    public static function isStringInFileAfterString(
        string $filename,
        string $string,
        string $afterString,
        int $linesAfterString
    )
    {
        $lines = file($filename);
        $trimmedLines = array_map('trim', $lines);
        $numberOfMatchedLine = array_search($string, $trimmedLines);

        return $trimmedLines[$numberOfMatchedLine + $linesAfterString] === $afterString;
    }

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
                    if (strpos($fileLine,'<?php') !== false)
                    {
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
                    if (strpos($fileLine,'protected static $doktype') !== false &&
                        strpos($fileLine,'protected static $doktype = 0;') === false
                    )
                    {
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
                if (strpos($file,'.php')) {
                    $result[] = $file;
                }
            }
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
