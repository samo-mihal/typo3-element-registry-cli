<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Utility;

use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Class Typo3ElementRegistryCliUtility
 * @package Digitalwerk\Typo3ElementRegistryCli\Utility
 */
class Typo3ElementRegistryCliUtility
{
    /**
     * Get Doktpy icon identifier
     * @param int $doktype
     * @return string
     */
    public static function getDoktypeIconIdentifier(int $doktype): string
    {
        return "dw-page-type-{$doktype}";
    }

    /**
     * Adds new page type
     * USAGE: in ext_tables.php
     *        add line: Digitalwerk\DwPageTypes\Utility\PageTypeUtility::addPageDoktype([DOKTYPE_NUMBER]);
     *
     * @param int $doktype
     * @throws    \Exception
     */
    public static function addPageDoktype(int $doktype)
    {
        if (array_key_exists($doktype, $GLOBALS['PAGES_TYPES'])) {
            throw new \Exception("Page type with doktype: {$doktype} already exists!", 1485421360);
        }

        // Add new page type
        $GLOBALS['PAGES_TYPES'][$doktype] = [
            'type' => 'web',
            'allowedTables' => '*',
        ];

        // Provide icon for page tree, list view, ... :
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Imaging\IconRegistry::class
        );
        $iconIdentifier = self::getDoktypeIconIdentifier($doktype);
        $iconRegistry->registerIcon(
            $iconIdentifier,
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => "EXT:dw_page_types/Resources/Public/Icons/{$iconIdentifier}.svg"]
        );
        $iconRegistry->registerIcon(
            $iconIdentifier.'-not-in-menu',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => "EXT:dw_page_types/Resources/Public/Icons/{$iconIdentifier}-not-in-menu.svg"]
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig(
            'options.pageTree.doktypesToShowInNewPageDragArea := addToList(' . $doktype . ')'
        );
    }

    /**
     * Adds new doktype to TCA select
     * USAGE: in Configuration/TCA/Overrides/
     *        Digitalwerk\DwPageTypes\Utility\PageTypeUtility::addTcaDoktype(
     *            [DOKTYPE_NUMBER],
     *            'after:99'
     *        );
     * @param int    $doktype
     * @param string $position
     */
    public static function addTcaDoktype(int $doktype, string $position = '')
    {
        $iconIdentifier = self::getDoktypeIconIdentifier($doktype);

        if ($position !== '') {
            list($relativePosition, $relativeToField) = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(':', $position);
        } else {
            $relativePosition = 'after';
            $relativeToField = '1';
        }

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
            'pages',
            'doktype',
            [
                "LLL:EXT:dw_page_types/Resources/Private/Language/locallang_db.xlf:page.type.{$doktype}.label",
                $doktype,
                $iconIdentifier,
            ],
            $relativeToField,
            $relativePosition
        );

        \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule(
            $GLOBALS['TCA']['pages'],
            [
                'ctrl' => [
                    'typeicon_classes' => [
                        $doktype => $iconIdentifier,
                        $doktype.'-hideinmenu' => "{$iconIdentifier}-not-in-menu",
                    ],
                ],
                'types' => [
                    $doktype => $GLOBALS['TCA']['pages']['types'][(string)PageRepository::DOKTYPE_DEFAULT],
                ],
            ]
        );
    }
}
