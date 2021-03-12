<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Utility;

use Symfony\Component\Console\Output\Output;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use function Symfony\Component\String\u;

/**
 * Class ImageUtility
 * @package Digitalwerk\Typo3ElementRegistryCli\Utility
 */
class ImageUtility
{
    /**
     * @param string $path
     * @param string $filename
     */
    public static function copyIcon(string $path, string $filename)
    {
        copy(
            GeneralUtility::getFileAbsFileName(
                'EXT:content_element_registry/Resources/Public/Icons/CEDefaultIcon.svg'
            ),
            GeneralUtility::getFileAbsFileName(
                $path . '/' . $filename . '.svg'
            )
        );
    }

    /**
     * @param string $path
     * @param string $filename
     */
    public static function copyImage(string $path, string $filename)
    {
        copy(
            GeneralUtility::getFileAbsFileName(
                'EXT:content_element_registry/Resources/Public/Images/NewContentElement1.png'
            ),
            GeneralUtility::getFileAbsFileName(
                $path . '/' . $filename . '.png'
            )
        );
    }

    /**
     * @param string $extension
     * @param string $iconName
     * @param Output $output
     */
    public static function registerIcon(string $extension, string $iconName, Output $output)
    {
        $path = GeneralUtility::getFileAbsFileName(
            'EXT:' . $extension . '/ext_localconf.php'
        );
        $originalContent = file_get_contents($path);
        if (u($originalContent)->indexOf('/** Registered icons */') === null) {
            $output->writeln(
                '<bg=red;options=bold>Register icon ' . $iconName . '</>'
            );
        } else {
            $spaces = u($originalContent)->before('/** Registered icons */')->afterLast("\n");
            $before = u($originalContent)->before('/** Registered icons */', true);
            $after = $before . "\n" . $spaces . "'" . u($iconName)->trimEnd() . "',";

            $originalContent = u($originalContent)->replace($before, $after);
        }

        file_put_contents($path, $originalContent);
    }
}
