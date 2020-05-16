<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TypoScriptRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
class TypoScriptRender extends AbstractRender
{
    /**
     * @var FieldsRender
     */
    protected $fieldsRender = null;

    /**
     * TypoScript constructor.
     * @param ElementRender $elementRender
     */
    public function __construct(ElementRender $elementRender)
    {
        parent::__construct($elementRender);
        $this->fieldsRender = GeneralUtility::makeInstance(FieldsRender::class, $elementRender);
    }

    /**
     * @param null $recordType
     * @return string
     * Return TypoScript Mapping (format string)
     */
    public function getTypoScriptMapping($recordType = null)
    {
        $mappingFields = $this->fieldsRender->fieldsToTypoScriptMapping();
        $table = $this->elementRender->getElement()->getTable();
        $pathToModel = $this->elementRender->getElement()->getModelNamespace() . '\\' . $this->elementRender->getElement()->getName();
        if (empty($recordType)) {
            $recordType =
                str_replace('_', '', $this->elementRender->getElement()->getExtensionName()) .
                '_' .
                strtolower($this->elementRender->getElement()->getStaticName()) .
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
        $extensionName = $this->elementRender->getElement()->getExtensionName();

//        TODO: synchronize function with add fields function
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
    public function pageTypeTypoScriptConstants()
    {
        $pageTypeName = $this->elementRender->getElement()->getName();
        $mainExtension = $this->elementRender->getElement()->getMainExtension();
        GeneralCreateCommandUtility::importStringInToFileAfterString(
            $this->elementRender->getElement()->getPathToTypoScriptConstants(),
            [
                "PAGE_DOKTYPE_" . strtoupper($pageTypeName) . " = " . $this->elementRender->getElement()->getDoktype() . " \n"
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
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function pageTypeTypoScriptSubclassOfDefaultPage()
    {
        $extensionName = $this->elementRender->getElement()->getExtensionName();
        $pageTypeName = $this->elementRender->getElement()->getName();
        $modelNameSpace = $this->elementRender->getElement()->getModelNamespace();

        GeneralCreateCommandUtility::importStringInToFileAfterString(
            'public/typo3conf/ext/' . $extensionName . '/ext_typoscript_setup.typoscript',
            [
                "          " . $modelNameSpace . "\\" . $pageTypeName . " = " . $modelNameSpace . "\\" . $pageTypeName. " \n"
            ],
            $this->elementRender->getElement()->getPageTypeModelExtendClass() . ' {',
            5
        );
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function pageTypeTypoScriptRegister()
    {
        $extensionName = $this->elementRender->getElement()->getExtensionName();
        $pageTypeName = $this->elementRender->getElement()->getName();

//        TODO: synchronize function with add fields function
        GeneralCreateCommandUtility::importStringInToFileAfterString(
            'public/typo3conf/ext/' . $extensionName . '/ext_typoscript_setup.typoscript',
            [
                $this->getTypoScriptMapping('{$PAGE_DOKTYPE_' . strtoupper($pageTypeName) . '}') . " \n"
            ],
            'config.tx_extbase {',
            2
        );
    }

    public function addPluginToWizard()
    {
        $pluginName = $this->elementRender->getElement()->getName();
        $extensionName = $this->elementRender->getElement()->getExtensionName();

        GeneralCreateCommandUtility::importStringInToFileAfterString(
            'public/typo3conf/ext/' . $this->elementRender->getElement()->getMainExtension() . '/Configuration/TSconfig/Page/Includes/Mod.tsconfig',
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
