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
     * @return void
     * Return TypoScript Mapping (format string)
     */
    public function mapTypoScript($recordType = null): void
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
                strtolower($this->element->getName());
        }
        $fieldsInTypoScriptColumn = '          columns {' . "\n" .
            '            ' . $mappingFields . "\n".
            '          }';

        $template[] = '     ' . $pathToModel . ' {';
        $template[] = '        mapping {';
        $template[] = '          tableName = ' . $table;
        $template[] = '          recordType = ' . $recordType;
        if ($mappingFields) {
            $template[] = $fieldsInTypoScriptColumn;
        }
        $template[] = '        }';
        $template[] = '      }';


        if (
            GeneralCreateCommandUtility::isStringInFileAfterString(
                $this->element->getTypoScriptPath(),
                'recordType = ' . $recordType,
                'columns {',
                1
            )
        ) {
            if ($mappingFields) {
                GeneralCreateCommandUtility::importStringInToFileAfterString(
                    $this->element->getTypoScriptPath(),
                    [
                        '            ' . $mappingFields . "\n"
                    ],
                    'columns {',
                    0
                );
            }
        } else {
            GeneralCreateCommandUtility::importStringInToFileAfterString(
                $this->element->getTypoScriptPath(),
                [
                   $fieldsInTypoScriptColumn . "\n"
                ],
                'recordType = ' . $recordType,
                0,
                [
                    'newLines' => implode("\n", $template) . "\n",
                    'universalStringInFile' => 'config.tx_extbase {',
                    'linesAfterSpecificString' => 2
                ]
            );
        }
    }

    /**
     * @return void
     */
    public function inlineMapping(): void
    {
        $this->mapTypoScript();
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function pageTypeTypoScriptConstants()
    {
        $pageTypeName = $this->elementRender->getElement()->getName();
        GeneralCreateCommandUtility::importStringInToFileAfterString(
            $this->elementRender->getElement()->getPathToTypoScriptConstants(),
            [
                "PAGE_DOKTYPE_" . strtoupper($pageTypeName) . " = " . $this->elementRender->getElement()->getDoktype() . " \n"
            ],
            '#Page types',
            1
        );

        GeneralCreateCommandUtility::importStringInToFileAfterString(
            $this->element->getTypoScriptMainExtensionConfigPath(),
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
        $pageTypeName = $this->elementRender->getElement()->getName();
        $modelNameSpace = $this->elementRender->getElement()->getModelNamespace();

        GeneralCreateCommandUtility::importStringInToFileAfterString(
            $this->element->getExtTypoScriptSetupPath(),
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
        $pageTypeName = $this->elementRender->getElement()->getName();
        $this->mapTypoScript('{$PAGE_DOKTYPE_' . strtoupper($pageTypeName) . '}');
    }

    public function addPluginToWizard()
    {
        $pluginName = $this->elementRender->getElement()->getName();
        $extensionName = $this->elementRender->getElement()->getExtensionName();

        GeneralCreateCommandUtility::importStringInToFileAfterString(
            $this->element->getModTSConfigPath(),
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
