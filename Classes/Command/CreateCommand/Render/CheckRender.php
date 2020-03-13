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
                'data' => $this->extLocalConfContentElementCreateCommandBasicStructure()
            ],
            'public/typo3conf/ext/' . $extensionName . '/ext_tables.sql' => [
                'path' => 'public/typo3conf/ext/' . $extensionName . '/ext_tables.sql',
                'data' => "\n"
            ],
            'public/typo3conf/ext/' . $extensionName . '/ext_typoscript_setup.typoscript' => [
                'path' => 'public/typo3conf/ext/' . $extensionName . '/ext_typoscript_setup.typoscript',
                'data' => $this->typoScriptContentElementCreateCommandBasicStructure()
            ],
            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf' => [
                'path' => 'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf',
                'data' => $this->localLangContentElementCreateCommandBasicStructure()
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
    public function typoScriptContentElementCreateCommandBasicStructure()
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
    public function localLangContentElementCreateCommandBasicStructure()
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
    public function extLocalConfContentElementCreateCommandBasicStructure()
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
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public static function createCommandCustomDataCheck()
    {
        $mainExtension = GeneralUtility::makeInstance(RunCreateElementCommand::class)->getMainExtension();
        $vendor = GeneralUtility::makeInstance(RunCreateElementCommand::class)->getVendor();
        $extensionName = $mainExtension;
        $extensionNameInNameSpace = str_replace(' ','',ucwords(str_replace('_',' ',$extensionName)));

        if (!file_exists('public/typo3conf/ext/' . $extensionName . '/Classes/CreateCommandConfig/CreateCommandCustomData.php'))
        {
            if (!file_exists('public/typo3conf/ext/' . $extensionName . '/Classes/CreateCommandConfig/')) {
                mkdir('public/typo3conf/ext/' . $extensionName . '/Classes/CreateCommandConfig/', 0777, true);
            }

            file_put_contents(
                'public/typo3conf/ext/' . $extensionName . '/Classes/CreateCommandConfig/CreateCommandCustomData.php',
                '<?php
namespace ' . $vendor . '\\' . $extensionNameInNameSpace . '\CreateCommandConfig;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;

/**
 * Class CreateCommandCustomData
 * @package ' . $vendor . '\\' . $extensionNameInNameSpace . '\CreateCommandConfig
 */
class CreateCommandCustomData
{
    /**
     * @return array
     */
    public function typo3FieldTypes()
    {
        return [];
    }

    /**
     * @param FieldObject $field
     * @return array
     */
    public function newFieldsConfigs(FieldObject $field)
    {
        $fieldType = $field->getType();

        return [];
    }

    /**
     * @return string
     */
    protected function getNewFieldConfig()
    {
        return "";
    }

    /**
     * @param FieldObject $field
     * @return array
     */
    public function newFieldsModelDescription(FieldObject $field)
    {
        $fieldType = $field->getType();

        return [];
    }

    /**
     * @return array
     */
    protected function getDateDescription()
    {
        return [];
    }

    /**
     * @return array
     */
    public function traitsAndClasses()
    {
        return [];
    }

    /**
     * @return string
     */
    public function overrideContentElementAndInlineModelExtendClass()
    {
        return "";
    }
}
'
            );
        }
    }
}
