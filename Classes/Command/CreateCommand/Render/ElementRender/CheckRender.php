<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

/**
 * Class CheckRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
class CheckRender extends AbstractRender
{
    /**
     * CheckRender constructor.
     * @param ElementRender $elementRender
     */
    public function __construct(ElementRender $elementRender)
    {
        parent::__construct($elementRender);
    }

    /**
     * @return void
     */
    public function contentElementCreateCommand(): void
    {
        $requiredFolders = [
            $this->element->getContentElementClassDirPath(),
            $this->element->getModelDirPath(),
            $this->element->getTemplateDirPath(),
            $this->element->getConfigurationOverridesDirPath(),
            $this->element->getIconDirPath(),
            $this->element->getPreviewDirPath(),
            $this->element->getTranslationDirPath()
        ];
        $this->checkAndCreateFolders($requiredFolders);

        $requiredFiles = [
            $this->element->getExtLocalConfPath() => [
                'path' => $this->element->getExtLocalConfPath(),
                'data' => $this->extLocalConfBasicStructure()
            ],
            $this->element->getExtTablesSqlPath() => [
                'path' => $this->element->getExtTablesSqlPath(),
                'data' => "\n"
            ],
            $this->element->getExtTypoScriptSetupPath() => [
                'path' => $this->element->getExtTypoScriptSetupPath(),
                'data' => $this->typoScriptBasicStructure()
            ],
            $this->element->getTranslationPath() => [
                'path' => $this->element->getTranslationPath(),
                'data' => $this->localLangBasicStructure()
            ]
        ];
        $this->checkAndCreateFiles($requiredFiles);
    }

    /**
     * @return void
     */
    public function pageTypeCreateCommand(): void
    {
        $requiredFolders = [
            $this->element->getModelDirPath(),
            $this->element->getConfigurationOverridesDirPath(),
            $this->element->getIconDirPath(),
            $this->element->getTranslationDirPath()
        ];
        $this->checkAndCreateFolders($requiredFolders);

        $requiredFiles = [
            $this->element->getExtTablesPhpPath() => [
                'path' => $this->element->getExtTablesPhpPath(),
                'data' => $this->extTableBasicStructure()
            ],
            $this->element->getExtTablesSqlPath() => [
                'path' => $this->element->getExtTablesSqlPath(),
                'data' => "\n"
            ],
            $this->element->getExtTypoScriptSetupPath() => [
                'path' => $this->element->getExtTypoScriptSetupPath(),
                'data' => $this->typoScriptBasicStructure()
            ],
            $this->element->getTranslationPath() => [
                'path' => $this->element->getTranslationPath(),
                'data' => $this->localLangBasicStructure()
            ]
        ];
        $this->checkAndCreateFiles($requiredFiles);
    }

    /**
     * @return void
     */
    public function pluginCreateCommand(): void
    {
        $requiredFolders = [
            $this->element->getControllerDirPath(),
            $this->element->getTemplateDirPath(),
            $this->element->getConfigurationOverridesDirPath(),
            $this->element->getFlexFormDirPath(),
            $this->element->getIconDirPath(),
            $this->element->getPreviewDirPath(),
            $this->element->getTranslationDirPath()
        ];
        $this->checkAndCreateFolders($requiredFolders);

        $requiredFiles = [
            $this->element->getExtLocalConfPath() => [
                'path' => $this->element->getExtLocalConfPath(),
                'data' => $this->extLocalConfBasicStructure()
            ],
            $this->element->getTranslationPath() => [
                'path' => $this->element->getTranslationPath(),
                'data' => $this->localLangBasicStructure()
            ],
            $this->element->getTtContentPath() => [
                'path' => $this->element->getTtContentPath(),
                'data' => $this->ttContentBasicStructure()
            ]
        ];
        $this->checkAndCreateFiles($requiredFiles);
    }

    /**
     * @return void
     */
    public function recordCreateCommand(): void
    {
        $requiredFolders = [
            $this->element->getModelDirPath(),
            $this->element->getTCADirPath(),
            $this->element->getIconDirPath(),
            $this->element->getTranslationDirPath()
        ];
        $this->checkAndCreateFolders($requiredFolders);

        $requiredFiles = [
            $this->element->getExtLocalConfPath() => [
                'path' => $this->element->getExtLocalConfPath(),
                'data' => $this->extLocalConfBasicStructure()
            ],
            $this->element->getExtTablesSqlPath() => [
                'path' => $this->element->getExtTablesSqlPath(),
                'data' => "\n"
            ],
            $this->element->getExtTypoScriptSetupPath() => [
                'path' => $this->element->getExtTypoScriptSetupPath(),
                'data' => $this->typoScriptBasicStructure()
            ],
            $this->element->getTranslationPath() => [
                'path' => $this->element->getTranslationPath(),
                'data' => $this->localLangBasicStructure()
            ]
        ];
        $this->checkAndCreateFiles($requiredFiles);
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
