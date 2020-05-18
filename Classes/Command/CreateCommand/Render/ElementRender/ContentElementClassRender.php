<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ContentElementClassRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
class ContentElementClassRender extends AbstractRender
{
    /**
     * @var FieldsRender
     */
    protected $fieldsRender = null;

    /**
     * ContentElementClass constructor.
     * @param ElementRender $elementRender
     */
    public function __construct(ElementRender $elementRender)
    {
        parent::__construct($elementRender);
        $this->fieldsRender = GeneralUtility::makeInstance(FieldsRender::class, $elementRender);
    }

    public function columnMapping()
    {
        $fieldsToClassMapping = $this->fieldsRender->fieldsToClassMapping();

        if ($fieldsToClassMapping) {
            $view = clone $this->view;
            $view->setTemplatePathAndFilename(
                GeneralUtility::getFileAbsFileName(
                    'EXT:typo3_element_registry_cli/Resources/Private/Templates/ContentElementClass/ContentElementClassColumnMappingTemplate.html'
                )
            );
            $view->assignMultiple([
                'fieldsToClassMapping' => $fieldsToClassMapping
            ]);

            $this->importStringRender->importStringInToFileAfterString(
                $this->element->getContentElementClassPath(),
                ElementObject::FIELDS_TAB. ElementObject::FIELDS_TAB . $fieldsToClassMapping . ",\n",
                'protected $columnsMapping = [',
                0,
                [
                    'newLines' => $view->render(),
                    'universalStringInFile' => '{',
                    'linesAfterSpecificString' => 0
                ]
            );
        }
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function columnOverride()
    {
        $fieldsToColumnsOverrides = $this->fieldsRender->fieldsToColumnsOverrides();

        if ($fieldsToColumnsOverrides) {
            $view = clone $this->view;
            $view->setTemplatePathAndFilename(
                GeneralUtility::getFileAbsFileName(
                    'EXT:typo3_element_registry_cli/Resources/Private/Templates/ContentElementClass/ContentElementClassColumnOverrideTemplate.html'
                )
            );
            $view->assignMultiple([
                'fieldsToColumnsOverrides' => $fieldsToColumnsOverrides
            ]);

            $this->importStringRender->importStringInToFileAfterString(
                $this->element->getContentElementClassPath(),
                ElementObject::FIELDS_TAB . ElementObject::FIELDS_TAB .
                ElementObject::FIELDS_TAB . $fieldsToColumnsOverrides . "\n",
                'public function getColumnsOverrides()',
                2,
                [
                    'newLines' => $view->render(),
                    'universalStringInFile' => '}',
                    'linesAfterSpecificString' => 0
                ]
            );
        }
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function palette()
    {
        $fieldsToPalette = $this->fieldsRender->fieldsToPalette();
        if ($fieldsToPalette) {
            $view = clone $this->view;
            $view->setTemplatePathAndFilename(
                GeneralUtility::getFileAbsFileName(
                    'EXT:typo3_element_registry_cli/Resources/Private/Templates/ContentElementClass/ContentElementClassPaletteTemplate.html'
                )
            );
            $view->assignMultiple([
                'fieldsToPalette' => $fieldsToPalette
            ]);

            $this->importStringRender->importStringInToFileAfterString(
                $this->element->getContentElementClassPath(),
                ElementObject::FIELDS_TAB . ElementObject::FIELDS_TAB .
                ElementObject::FIELDS_TAB . '--linebreak--, ' . $fieldsToPalette . ",\n",
                '\'default\',',
                1,
                [
                    'newLines' => $view->render(),
                    'universalStringInFile' => 'parent::__construct();',
                    'linesAfterSpecificString' => 0
                ]
            );
        }

    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function template()
    {
        $filename = $this->element->getContentElementClassPath();
        if (!file_exists($filename)) {
            $view = clone $this->view;
            $view->setTemplatePathAndFilename(
                GeneralUtility::getFileAbsFileName(
                    'EXT:typo3_element_registry_cli/Resources/Private/Templates/ContentElementClass/ContentElementClassTemplate.html'
                )
            );
            $view->assignMultiple([
                'vendor' => $this->elementRender->getElement()->getVendor(),
                'name' => $this->elementRender->getElement()->getName(),
                'extensionName' => $this->elementRender->getElement()->getExtensionNameSpaceFormat()
            ]);
            file_put_contents(
                $filename,
                $view->render()
            );
        }

        $this->columnMapping();
        $this->palette();
        $this->columnOverride();
    }
}
