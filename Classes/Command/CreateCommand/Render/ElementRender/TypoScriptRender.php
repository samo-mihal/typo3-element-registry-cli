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
     * @param ElementRender $element
     */
    public function __construct(ElementRender $element)
    {
        parent::__construct($element);
        $this->fieldsRender = GeneralUtility::makeInstance(FieldsRender::class, $element);
    }

    /**
     * @param null $recordType
     * @return string
     * Return TypoScript Mapping (format string)
     */
    public function getTypoScriptMapping($recordType = null)
    {
        $mappingFields = $this->fieldsRender->fieldsToTypoScriptMapping();
        $table = $this->element->getTable();
        $pathToModel = $this->element->getModelNamespace() . '\\' . $this->element->getName();
        if (empty($recordType)) {
            $recordType =
                str_replace('_', '', $this->element->getExtensionName()) .
                '_' .
                strtolower($this->element->getStaticName()) .
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
        $extensionName = $this->element->getExtensionName();

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
        $extensionName = $this->element->getExtensionName();
        $pageTypeName = $this->element->getName();
        $modelNameSpace = $this->element->getModelNamespace();
        $mainExtension = $this->element->getMainExtension();
        GeneralCreateCommandUtility::importStringInToFileAfterString(
            $this->element->getPathToTypoScriptConstants(),
            [
                "PAGE_DOKTYPE_" . strtoupper($pageTypeName) . " = " . $this->element->getDoktype() . " \n"
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
            $this->element->getPageTypeModelExtendClass() . ' {',
            5
        );
    }

    public function addPluginToWizard()
    {
        $pluginName = $this->element->getName();
        $extensionName = $this->element->getExtensionName();

        GeneralCreateCommandUtility::importStringInToFileAfterString(
            'public/typo3conf/ext/' . $this->element->getMainExtension() . '/Configuration/TSconfig/Page/Includes/Mod.tsconfig',
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
