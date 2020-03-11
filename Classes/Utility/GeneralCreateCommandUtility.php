<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Utility;

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
     * @return bool
     * if filename does not exist return false
     */
    public static function importStringInToFileAfterString(string $filename, array $newLines, string $universalStringInFile, int $linesAfterSpecificString)
    {
        $lines = file($filename);
        $trimmedLines = array_map('trim', $lines);
        $numberOfMatchedLine = array_search($universalStringInFile, $trimmedLines);
        if ($numberOfMatchedLine !== false) {
            $lines = self::arrayInsertAfter($lines,$numberOfMatchedLine + $linesAfterSpecificString, $newLines);
            file_put_contents($filename, $lines);
            return true;
        } else {
            return false;
        }
    }
}
