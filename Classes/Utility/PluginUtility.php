<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Utility;

use Symfony\Component\Console\Output\Output;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use function Symfony\Component\String\u;

/**
 * Class PluginUtility
 * @package Digitalwerk\Typo3ElementRegistryCli\Utility
 */
class PluginUtility
{
    /**
     * @param string $path
     * @param string $content
     * @param Output $output
     */
    public static function registerPlugin(string $path, string $content, Output $output)
    {
        $path = GeneralUtility::getFileAbsFileName($path);
        $originalContent = file_get_contents($path);
        if (u($originalContent)->indexOf("defined('TYPO3_MODE') or die();") === null) {
            $output->writeln(
                '<bg=red;options=bold>Add ' . $content . ' to ' . $path . '</>'
            );
        } else {
            $before = u($originalContent)->before("defined('TYPO3_MODE') or die();", true);
            $after = $before . "\n\n" . u($content)->trimEnd();

            $originalContent = u($originalContent)->replace($before, $after);
        }

        file_put_contents($path, $originalContent);
    }

    /**
     * @param string $path
     * @param string $content
     * @param Output $output
     */
    public static function configPlugin(string $path, string $content, Output $output)
    {
        $path = GeneralUtility::getFileAbsFileName($path);
        $originalContent = file_get_contents($path);
        if (u($originalContent)->indexOf('/** Plugins configuration */') === null) {
            $output->writeln(
                '<bg=red;options=bold>Add ' . $content . ' to ' . $path . '</>'
            );
        } else {
            $before = u($originalContent)->before('/** Plugins configuration */', true);
            $after = $before . "\n" . u($content)->trimEnd();

            $originalContent = u($originalContent)->replace($before, $after);
        }

        file_put_contents($path, $originalContent);
    }
}
