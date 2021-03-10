<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Utility;

use Symfony\Component\Console\Output\Output;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use function Symfony\Component\String\u;

/**
 * Class TranslationUtility
 * @package Digitalwerk\Typo3ElementRegistryCli\Utility
 */
class TyposcriptUtility
{
    /**
     * @param string $path
     * @param string $content
     * @param Output $output
     */
    public static function addToExtbasePersistenceClasses(string $path, string $content, Output $output)
    {
        $path = GeneralUtility::getFileAbsFileName($path);
        $originalContent = file_get_contents($path);
        if (u($originalContent)->indexOf('classes {') === null) {
            $output->writeln(
                '<bg=red;options=bold>Add' . $content .
                'to typoscript extbase->persistence->classes' . '</>'
            );
        } else {
            $before = u($originalContent)->before('classes {', true);
            $after = $before . u($content)->trimEnd();

            $originalContent = u($originalContent)->replace($before, $after);
        }

        file_put_contents($path, $originalContent);
    }

    /**
     * @param string $path
     * @param string $constant
     * @param Output $output
     */
    public static function addToConstants(string $path, string $constant, Output $output)
    {
        $path = GeneralUtility::getFileAbsFileName($path);
        $originalContent = file_get_contents($path);
        if (u($originalContent)->indexOf('#Page types') === null) {
            $output->writeln(
                '<bg=red;options=bold>Add typoscript constant: ' . $constant . '</>'
            );
        } else {
            $before = u($originalContent)->before('#Page types', true);
            $after = $before . "\n" . $constant;

            $originalContent = u($originalContent)->replace($before, $after);
        }

        file_put_contents($path, $originalContent);
    }
}
