<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
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


        if ($this->importStringRender->isStringInFileAfterString
            (
                $this->element->getTypoScriptPath(),
                'recordType = ' . $recordType,
                'columns {',
                1
            )
        ) {
            if ($mappingFields) {
                $this->importStringRender->importStringInToFileAfterString(
                    $this->element->getTypoScriptPath(),
                    ElementObject::FIELDS_TAB . ElementObject::FIELDS_TAB . ElementObject::FIELDS_TAB .
                    $mappingFields . "\n",
                    'recordType = ' . $recordType,
                    1
                );
            }
        } else {
            $this->importStringRender->importStringInToFileAfterString(
                $this->element->getTypoScriptPath(),
                $fieldsInTypoScriptColumn . "\n",
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
        $this->importStringRender->importStringInToFileAfterString(
            $this->elementRender->getElement()->getPathToTypoScriptConstants(),
            "PAGE_DOKTYPE_" . strtoupper($pageTypeName) . " = " . $this->elementRender->getElement()->getDoktype() . " \n",
            '#Page types',
            1
        );

        $this->importStringRender->importStringInToFileAfterString(
            $this->element->getTypoScriptMainExtensionConfigPath(),
            ElementObject::FIELDS_TAB . ElementObject::FIELDS_TAB . ElementObject::FIELDS_TAB .
            ElementObject::FIELDS_TAB . strtolower($pageTypeName) . ' = {$PAGE_DOKTYPE_' . strtoupper($pageTypeName) .
            '}' . " \n",
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

        $this->importStringRender->importStringInToFileAfterString(
            $this->element->getExtTypoScriptSetupPath(),
            ElementObject::FIELDS_TAB . ElementObject::FIELDS_TAB .
            "  " . $modelNameSpace . "\\" . $pageTypeName . " = " . $modelNameSpace . "\\" . $pageTypeName. " \n",
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

    /**
     * @return void
     */
    public function addPluginToWizard(): void
    {
        $view = clone $this->view;
        $view->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName(
                'EXT:typo3_element_registry_cli/Resources/Private/Templates/Wizard/PluginTemplate.html'
            )
        );
        $view->assignMultiple([
            'name' => $this->element->getName(),
            'llPath' => $this->element->getTranslationPathShort(),
            'extensionNameInNameSpaceFormat' => $this->element->getExtensionNameSpaceFormat()
        ]);

        $this->importStringRender->importStringInToFileAfterString(
            $this->element->getModTSConfigPath(),
            $view->render(),
            "plugins {",
            1
        );
    }
}
