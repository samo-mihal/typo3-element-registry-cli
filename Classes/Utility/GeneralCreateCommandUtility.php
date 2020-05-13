<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Utility;

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
     * @return void if filename does not exist return false
     * if filename does not exist return false
     */
    public static function importStringInToFileAfterString(string $filename, array $newLines, string $universalStringInFile, int $linesAfterSpecificString, array $onFail = [])
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
        }
    }

    /**
     * @param string $filename
     * @param string $universalStringInFile
     * @param int $linesAfterString
     * @param string $afterString
     * @param int $positionAfterString
     * @param string $insertStr
     * @return string
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
}
