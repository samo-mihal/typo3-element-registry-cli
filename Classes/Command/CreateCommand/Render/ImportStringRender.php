<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;
use InvalidArgumentException;

/**
 * Class ElementRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render
 */
class ImportStringRender
{
    /**
     * @var ElementObject
     */
    protected $elementObject = null;

    /**
     * ImportStringRender constructor.
     * @param ElementObject $elementObject
     */
    public function __construct(ElementObject $elementObject)
    {
        $this->elementObject = $elementObject;
    }

    /**
     * @param array $array
     * @param $key
     * @param array $new
     * @return array
     */
    public function arrayInsertAfter( array $array, $key, array $new ) {
        $keys = array_keys( $array );
        $index = array_search( $key, $keys );
        $pos = false === $index ? count( $array ) : $index + 1;
        return array_merge( array_slice( $array, 0, $pos ), $new, array_slice( $array, $pos ) );
    }

    /**
     * @param string $filename
     * @param string $newLines
     * @param string $universalStringInFile
     * @param int $linesAfterSpecificString
     * @param array $onFail
     * @return void if filename does not exist return false
     */
    public function importStringInToFileAfterString(
        string $filename,
        string $newLines,
        string $universalStringInFile,
        int $linesAfterSpecificString,
        array $onFail = []
    ): void
    {
        $lines = file($filename);
        $trimmedLines = array_map('trim', $lines);
        $numberOfMatchedLine = array_search($universalStringInFile, $trimmedLines);
        if (false !== $numberOfMatchedLine) {
            $lines = $this->arrayInsertAfter($lines,$numberOfMatchedLine + $linesAfterSpecificString, [$newLines]);
            file_put_contents($filename, $lines);
        } elseif (!empty($onFail)) {
            $numberOfMatchedLine = array_search($onFail['universalStringInFile'], $trimmedLines);
            $lines = $this->arrayInsertAfter($lines,$numberOfMatchedLine + $onFail['linesAfterSpecificString'], [$onFail['newLines']]);
            file_put_contents($filename, $lines);
        }
    }

    /**
     * @param string $filename
     * @param string $string
     * @param string $afterString
     * @param int $linesAfterString
     * @return bool
     */
    public function isStringInFileAfterString(
        string $filename,
        string $string,
        string $afterString,
        int $linesAfterString
    ): bool
    {
        $lines = file($filename);
        $trimmedLines = array_map('trim', $lines);
        $numberOfMatchedLine = array_search($string, $trimmedLines);

        return $trimmedLines[$numberOfMatchedLine + $linesAfterString] === $afterString;
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
    public function insertStringToFileInlineAfter(
        string $filename,
        string $universalStringInFile,
        int $linesAfterString,
        string $afterString,
        int $positionAfterString,
        string $insertStr): void
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
