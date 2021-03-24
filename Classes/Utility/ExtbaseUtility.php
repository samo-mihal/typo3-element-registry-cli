<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Utility;

use Symfony\Component\Console\Output\Output;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use function Symfony\Component\String\u;

/**
 * Class ExtbaseUtility
 * @package Digitalwerk\Typo3ElementRegistryCli\Utility
 */
class ExtbaseUtility
{
    /**
     * @param string $path
     * @param string $pageTypeClass
     * @param Output $output
     */
    public static function addToExtbasePersistenceClasses(string $path, string $pageTypeClass, Output $output)
    {
        $content = file_get_contents(
            GeneralUtility::getFileAbsFileName(
                'EXT:typo3_element_registry_cli/Resources/Private/Templates/Utility/Persistence/Classes/PageType.txt'
            )
        );
        $content = str_replace('{pageTypeClass}', $pageTypeClass, $content);

        $path = GeneralUtility::getFileAbsFileName($path);
        $originalContent = file_get_contents($path);
        if (u($originalContent)->indexOf('/** Page types */') === null) {
            $output->writeln(
                '<bg=red;options=bold>Add ' . $content . ' to ' . $path . '</>'
            );
        } else {
            $before = u($originalContent)->before('/** Page types */', true);
            $after = $before . "\n" . u($content)->trimEnd();

            $originalContent = u($originalContent)->replace($before, $after);
        }

        file_put_contents($path, $originalContent);
    }

    /**
     * @param string $path
     * @param string $content
     */
    public static function addPageTCA(string $path, string $content)
    {
        file_put_contents($path, $content);
    }
}
