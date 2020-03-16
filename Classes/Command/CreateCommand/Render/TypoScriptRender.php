<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;
use InvalidArgumentException;

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
     * TypoScript constructor.
     * @param RenderCreateCommand $render
     */
    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
    }

    /**
     * @return string
     */
    public function addFieldsToTypoScriptMapping()
    {
        $fields = $this->render->getFields();

        if (!empty($fields)) {
            $name = $this->render->getName();
            $createdFields = [];

            foreach ($fields->getFields() as $field) {
                $fieldName = $field->getName();
                $fieldType = $field->getType();

                if ($fieldName === $fieldType && $field->isDefault()) {
//                   Nothing to add (default fields)
                } elseif ($fieldName !== $fieldType && $field->isDefault()) {
                    $createdFields[] = $fieldType.'.mapOnProperty = '.str_replace(' ','',lcfirst(ucwords(str_replace('_',' ',$fieldName))));
                } elseif ($field->exist()) {
                    $createdFields[] = strtolower($name).'_'.$fieldName.'.mapOnProperty = '.str_replace(' ','',lcfirst(ucwords(str_replace('_',' ',$fieldName))));
                } else {
                    throw new InvalidArgumentException('Field "' . $fieldType . '" does not exist.2');
                }
            }

            return  implode('
            ', $createdFields);
        }
    }

    /**
     * @param null $recordType
     * @return string
     * Return TypoScript Mapping (format string)
     */
    public function getTypoScriptMapping($recordType = null)
    {
        $mappingFields = $this->addFieldsToTypoScriptMapping();
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
