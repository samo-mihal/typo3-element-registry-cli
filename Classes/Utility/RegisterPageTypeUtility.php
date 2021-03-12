<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Utility;

use Symfony\Component\Console\Output\Output;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use function Symfony\Component\String\u;

/**
 * Class RegisterPageTypeUtility
 * @package Digitalwerk\Typo3ElementRegistryCli\Utility
 */
class RegisterPageTypeUtility
{
    /**
     * @param string $path
     * @param string $content
     * @param Output $output
     */
    public static function registerDoktype(string $path, string $content, Output $output)
    {
        $path = GeneralUtility::getFileAbsFileName($path);
        $originalContent = file_get_contents($path);
        if (u($originalContent)->indexOf('/** Register page doktypes */') === null) {
            $output->writeln(
                '<bg=red;options=bold>Add ' . $content . ' to ' . $path . '</>'
            );
        } else {
            $spaces = u($originalContent)->before('/** Register page doktypes */')->afterLast("\n");
            $before = u($originalContent)->before('/** Register page doktypes */', true);
            $after = $before . "\n" . $spaces . u($content)->trimEnd();

            $originalContent = u($originalContent)->replace($before, $after);
        }

        file_put_contents($path, $originalContent);
    }

    /**
     * @param string $path
     * @param string $content
     * @param Output $output
     */
    public static function registerTCADoktype(string $path, string $content, Output $output)
    {
        $path = GeneralUtility::getFileAbsFileName($path);
        $originalContent = file_get_contents($path);
        if (u($originalContent)->indexOf('/** Add page doktypes */') === null) {
            $output->writeln(
                '<bg=red;options=bold>Add ' . $content . ' to ' . $path . '</>'
            );
        } else {
            $before = u($originalContent)->before('/** Add page doktypes */', true);
            $after = $before . "\n" . u($content)->trimEnd();

            $originalContent = u($originalContent)->replace($before, $after);
        }

        file_put_contents($path, $originalContent);
    }
}
