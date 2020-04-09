<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TypoScript
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render
 */
class TypoScriptRender
{
    /**
     * @var RenderCreateCommand
     */
    protected $render = null;

    /**
     * @var FieldsRender
     */
    protected $fieldsRender = null;

    /**
     * TypoScript constructor.
     * @param RenderCreateCommand $render
     */
    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
        $this->fieldsRender = GeneralUtility::makeInstance(FieldsRender::class, $render);
    }

    /**
     * @param null $recordType
     * @return string
     * Return TypoScript Mapping (format string)
     */
    public function getTypoScriptMapping($recordType = null)
    {
        $mappingFields = $this->fieldsRender->fieldsToTypoScriptMapping();
        $table = $this->render->getTable();
        $pathToModel = $this->render->getModelNamespace() . '\\' . $this->render->getName();
        if (empty($recordType)) {
            $recordType =
                str_replace('_', '', $this->render->getExtensionName()) .
                '_' .
                strtolower($this->render->getStaticName()) .
                '_' .
                strtolower(
                    end(
                        explode('\\', $pathToModel)
                    )
                );
        }

        $template[] = '     ' . $pathToModel . ' {';
        $template[] = '        mapping {';
        $template[] = '          tableName = ' . $table;
        $template[] = '          recordType = ' . $recordType;
        if ($mappingFields) {
            $template[] = '          columns {';
            $template[] = '            ' . $mappingFields;
            $template[] = '          }';
        }

        $template[] = '        }';
        $template[] = '      }';

        return implode("\n", $template);
    }

    public function inlineMapping()
    {
        $extensionName = $this->render->getExtensionName();

        GeneralCreateCommandUtility::importStringInToFileAfterString(
            'public/typo3conf/ext/' . $extensionName . '/ext_typoscript_setup.typoscript',
            [
                ' ' . $this->getTypoScriptMapping() . "\n"
            ],
            'config.tx_extbase {',
            2
        );
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function pageTypeTypoScriptRegister()
    {
        $extensionName = $this->render->getExtensionName();
        $pageTypeName = $this->render->getName();
        $modelNameSpace = $this->render->getModelNamespace();
        $mainExtension = $this->render->getMainExtension();
        GeneralCreateCommandUtility::importStringInToFileAfterString(
            $this->render->getPathToTypoScriptConstants(),
            [
                "PAGE_DOKTYPE_" . strtoupper($pageTypeName) . " = " . $this->render->getDoktype() . " \n"
            ],
            '#Page types',
            1
        );

        GeneralCreateCommandUtility::importStringInToFileAfterString(
            'public/typo3conf/ext/' . $mainExtension . '/Configuration/TypoScript/Extensions/' . str_replace(' ','',ucwords(str_replace('_',' ', $mainExtension))) . '.typoscript',
            [
                '                ' . strtolower($pageTypeName) . ' = {$PAGE_DOKTYPE_' . strtoupper($pageTypeName) . '}' . " \n"
            ],
            'doktype {',
            1
        );

        GeneralCreateCommandUtility::importStringInToFileAfterString(
            'public/typo3conf/ext/' . $extensionName . '/ext_typoscript_setup.typoscript',
            [
                $this->getTypoScriptMapping('{$PAGE_DOKTYPE_' . strtoupper($pageTypeName) . '}') . " \n"
            ],
            'config.tx_extbase {',
            2
        );

        GeneralCreateCommandUtility::importStringInToFileAfterString(
            'public/typo3conf/ext/' . $extensionName . '/ext_typoscript_setup.typoscript',
            [
                "          " . $modelNameSpace . "\\" . $pageTypeName . " = " . $modelNameSpace . "\\" . $pageTypeName. " \n"
            ],
            $this->render->getPageTypeModelExtendClass() . ' {',
            5
        );
    }

    public function addPluginToWizard()
    {
        $pluginName = $this->render->getName();
        $extensionName = $this->render->getExtensionName();

        GeneralCreateCommandUtility::importStringInToFileAfterString(
            'public/typo3conf/ext/' . $this->render->getMainExtension() . '/Configuration/TSconfig/Page/Includes/Mod.tsconfig',
            [
                "                        " . strtolower($pluginName) . " {
                            iconIdentifier = ". $pluginName . "
                            title = LLL:EXT:" . $extensionName . "/Resources/Private/Language/locallang_db.xlf:plugin." . strtolower($pluginName) . ".title
                            description = LLL:EXT:" . $extensionName . "/Resources/Private/Language/locallang_db.xlf:plugin." . strtolower($pluginName) . ".description
                            tt_content_defValues {
                                CType = list
                                list_type = " . str_replace('_', '', $extensionName) . "_" . strtolower($pluginName) . "
                            }
                        }\n"
            ],
            "plugins {",
            1

        );
    }
}
