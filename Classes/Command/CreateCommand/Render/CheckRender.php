<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use Digitalwerk\Typo3ElementRegistryCli\Command\RunCreateElementCommand;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class CheckRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render
 */
class CheckRender
{
    /**
     * @var null
     */
    protected $render = null;

    /**
     * TCA constructor.
     * @param RenderCreateCommand $render
     */
    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
    }

    public function contentElementCreateCommand()
    {
        $extensionName = $this->render->getExtensionName();
        $requiredFolders = [
            'public/typo3conf/ext/' . $extensionName . '/Classes/ContentElement',
            'public/typo3conf/ext/' . $extensionName . '/Classes/Domain/Model/ContentElement',
            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Templates/ContentElements',
            'public/typo3conf/ext/' . $extensionName . '/Configuration/TCA/Overrides',
            'public/typo3conf/ext/' . $extensionName . '/Resources/Public/Icons/ContentElement',
            'public/typo3conf/ext/' . $extensionName . '/Resources/Public/Images/ContentElementPreviews',
            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language'
        ];
        $this->checkAndCreateFolders($requiredFolders);

        $requiredFiles = [
            'public/typo3conf/ext/' . $extensionName . '/ext_localconf.php' => [
                'path' => 'public/typo3conf/ext/' . $extensionName . '/ext_localconf.php',
                'data' => $this->extLocalConfBasicStructure()
            ],
            'public/typo3conf/ext/' . $extensionName . '/ext_tables.sql' => [
                'path' => 'public/typo3conf/ext/' . $extensionName . '/ext_tables.sql',
                'data' => "\n"
            ],
            'public/typo3conf/ext/' . $extensionName . '/ext_typoscript_setup.typoscript' => [
                'path' => 'public/typo3conf/ext/' . $extensionName . '/ext_typoscript_setup.typoscript',
                'data' => $this->typoScriptBasicStructure()
            ],
            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf' => [
                'path' => 'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf',
                'data' => $this->localLangBasicStructure()
            ]
        ];
        $this->checkAndCreateFiles($requiredFiles);
    }

    public function pageTypeCreateCommand()
    {
        $extensionName = $this->render->getExtensionName();
        $requiredFolders = [
            'public/typo3conf/ext/' . $extensionName . '/Classes/Domain/Model',
            'public/typo3conf/ext/' . $extensionName . '/Configuration/TCA/Overrides',
            'public/typo3conf/ext/' . $extensionName . '/Resources/Public/Icons',
            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language'
        ];
        $this->checkAndCreateFolders($requiredFolders);

        $requiredFiles = [
            'public/typo3conf/ext/' . $extensionName . '/ext_tables.php' => [
                'path' => 'public/typo3conf/ext/' . $extensionName . '/ext_tables.php',
                'data' => $this->extTableBasicStructure()
            ],
            'public/typo3conf/ext/' . $extensionName . '/ext_tables.sql' => [
                'path' => 'public/typo3conf/ext/' . $extensionName . '/ext_tables.sql',
                'data' => "\n"
            ],
            'public/typo3conf/ext/' . $extensionName . '/ext_typoscript_setup.typoscript' => [
                'path' => 'public/typo3conf/ext/' . $extensionName . '/ext_typoscript_setup.typoscript',
                'data' => $this->typoScriptBasicStructure()
            ],
            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf' => [
                'path' => 'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf',
                'data' => $this->localLangBasicStructure()
            ]
        ];
        $this->checkAndCreateFiles($requiredFiles);
    }

    public function pluginCreateCommand()
    {
        $extensionName = $this->render->getExtensionName();
        $mainExtensionName = $this->render->getMainExtension();
        $requiredFolders = [
            'public/typo3conf/ext/' . $extensionName . '/Classes/Controller',
            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Templates',
            'public/typo3conf/ext/' . $extensionName . '/Configuration/TCA/Overrides',
            'public/typo3conf/ext/' . $extensionName . '/Configuration/FlexForms',
            'public/typo3conf/ext/' . $extensionName . '/Resources/Public/Icons',
            'public/typo3conf/ext/' . $mainExtensionName . '/Resources/Public/Images/ContentElementPreviews',
            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language'
        ];
        $this->checkAndCreateFolders($requiredFolders);

        $requiredFiles = [
            'public/typo3conf/ext/' . $extensionName . '/ext_localconf.php' => [
                'path' => 'public/typo3conf/ext/' . $extensionName . '/ext_localconf.php',
                'data' => $this->extLocalConfBasicStructure()
            ],
            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf' => [
                'path' => 'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf',
                'data' => $this->localLangBasicStructure()
            ],
            'public/typo3conf/ext/' . $extensionName . '/Configuration/TCA/Overrides/tt_content.php' => [
                'path' => 'public/typo3conf/ext/' . $extensionName . '/Configuration/TCA/Overrides/tt_content.php',
                'data' => $this->ttContentBasicStructure()
            ]
        ];
        $this->checkAndCreateFiles($requiredFiles);
    }

    public function recordCreateCommand()
    {
//        TODO: fill record create command
    }

    /**
     * @param $requiredFolders
     */
    public function checkAndCreateFolders($requiredFolders)
    {
        foreach ($requiredFolders as $requiredFolder) {
            if (!file_exists($requiredFolder)) {
                mkdir($requiredFolder, 0777, true);
            }
        }
    }

    /**
     * @param $requiredFiles
     */
    public function checkAndCreateFiles($requiredFiles)
    {
        foreach ($requiredFiles as $requiredFile) {
            if (!file_exists($requiredFile['path'])) {
                file_put_contents(
                    $requiredFile['path'],
                    $requiredFile['data']
                );
            }
        }
    }

    /**
     * @return string
     */
    public function typoScriptBasicStructure()
    {
        return 'config.tx_extbase {
  persistence {
    classes {
    }
  }
}';
    }

    /**
     * @return string
     */
    public function localLangBasicStructure()
    {
        return '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<xliff version="1.0">
    <file source-language="en" datatype="plaintext" original="messages" date="2015-08-14T12:33:16Z" product-name="content_element_registry">
        <header/>
        <body>
        </body>
    </file>
</xliff>';
    }

    /**
     * @return string
     */
    public function ttContentBasicStructure()
    {
        return '<?php

defined(\'TYPO3_MODE\') or die();
';
    }

    /**
     * @return string
     */
    public function extLocalConfBasicStructure()
    {
        return '<?php
if (!defined(\'TYPO3_MODE\')) {
    die(\'Access denied.\');
}

call_user_func(
    function ($extKey) {

    },
    $_EXTKEY
);
';
    }

    /**
     * @return string
     */
    public function extTableBasicStructure()
    {
        return '<?php


defined(\'TYPO3_MODE\') or die();

call_user_func(
    function () {
    }
);
';
    }
}
