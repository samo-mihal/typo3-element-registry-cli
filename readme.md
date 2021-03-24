# <img src="https://github.com/samo-mihal/typo3-element-registry-cli/raw/master/Resources/Public/Icons/Extension.svg?sanitize=true" width="40" height="40"/> Typo3 element registry CLI
Create a new elements (like Content element, Page type, etc..) with CLI.

## Install
Install extension via composer `composer require digitalwerk/typo3-element-registry-cli` and activate it in Extension module

## Setup
### Extension settings
After activating extension, you have to fill in extension settings.

#### General
- vendor

#### Content element
- classExtend (optional)
- modelExtend (optional)
- classTemplatePath (optional)
- modelTemplatePath (optional)
- templateTemplatePath (optional)

#### Page type
- typoScriptConstantsPath (required)
  - path to typoscript constants (Eg. EXT:{extension}/Configuration/TypoScript/constants.typoscript)
- utilityPath
    - Path to utility class
    - Utility class must contain addPageDoktype(int $doktype) static function
```php
<?php
declare(strict_types=1);
namespace Vendor\Extension\Utility;

use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Class PageTypeUtility
 * @package Vendor\Extension\Utility
 */
class PageTypeUtility
{

    /**
     * Get Doktype icon identifier
     * @param int $doktype
     * @return string
     */
    public static function getDoktypeIconIdentifier(int $doktype): string
    {
        return "{extension}-{$doktype}";
    }

    /**
     * Adds new page type
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
            ['source' => "EXT:{extension}/Resources/Public/Icons/{$iconIdentifier}.svg"]
        );
        $iconRegistry->registerIcon(
            $iconIdentifier.'-not-in-menu',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => "EXT:{extension}/Resources/Public/Icons/{$iconIdentifier}-not-in-menu.svg"]
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig(
            'options.pageTree.doktypesToShowInNewPageDragArea := addToList(' . $doktype . ')'
        );
    }

    /**
     * Adds new doktype to TCA select
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
                "LLL:EXT:{extension}/Resources/Private/Language/locallang_db.xlf:page.type.{$doktype}.label",
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


```
- modelExtend (optional)
- modelTemplatePath (optional)

#### Plugin
- controllerExtend (optional)

#### Record
- modelTemplatePath (optional)
- tcaTemplatePath (optional)

### Markers
#### /** Registered icons */
- Where: EXT:{extension}/ext_localconf.php

#### /** Plugins configuration */
- Where: EXT:{extension}/ext_localconf.php

#### /** Page types */
- Where: EXT:{extension}/Configuration/Extbase/Persistence/Classes.php

#### /** Add page doktypes */
- Where: EXT:{extension}/ext_tables.php

## Usage
### Commands
- dw:make:contentElement
- dw:make:pageType
- dw:make:record
- dw:make:plugin