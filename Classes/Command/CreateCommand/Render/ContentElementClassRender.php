<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class ContentElementClass
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render
 */
class ContentElementClassRender
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
     * @var StandaloneView
     */
    protected $view = null;

    /**
     * ContentElementClass constructor.
     * @param RenderCreateCommand $render
     */
    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
        $this->fieldsRender = GeneralUtility::makeInstance(FieldsRender::class, $render);
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
    }

    public function columnMapping()
    {
        $fieldsToClassMapping = $this->fieldsRender->fieldsToClassMapping();

        if ($fieldsToClassMapping) {
            $view = clone $this->view;
            $view->setTemplatePathAndFilename(
                GeneralUtility::getFileAbsFileName(
                    'EXT:typo3_element_registry_cli/Resources/Private/Templates/FieldsTemplate/ContentElementClassColumnMappingTemplate.html'
                )
            );
            $view->assignMultiple([
                'fieldsToClassMapping' => $fieldsToClassMapping
            ]);

            GeneralCreateCommandUtility::importStringInToFileAfterString(
                'public/typo3conf/ext/' . $this->render->getExtensionName() . '/Classes/ContentElement/' . $this->render->getName() . '.php',
                [
                    '        ' . $fieldsToClassMapping . ",\n"
                ],
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
                    'EXT:typo3_element_registry_cli/Resources/Private/Templates/FieldsTemplate/ContentElementClassColumnOverrideTemplate.html'
                )
            );
            $view->assignMultiple([
                'fieldsToColumnsOverrides' => $fieldsToColumnsOverrides
            ]);

            GeneralCreateCommandUtility::importStringInToFileAfterString(
                'public/typo3conf/ext/' . $this->render->getExtensionName() . '/Classes/ContentElement/' . $this->render->getName() . '.php',
                [
                    '            ' . $fieldsToColumnsOverrides . "\n"
                ],
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
                    'EXT:typo3_element_registry_cli/Resources/Private/Templates/FieldsTemplate/ContentElementClassPaletteTemplate.html'
                )
            );
            $view->assignMultiple([
                'fieldsToPalette' => $fieldsToPalette
            ]);

            GeneralCreateCommandUtility::importStringInToFileAfterString(
                'public/typo3conf/ext/' . $this->render->getExtensionName() . '/Classes/ContentElement/' . $this->render->getName() . '.php',
                [
                    '            --linebreak--, ' . $fieldsToPalette . ",\n"
                ],
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
        $filename = 'public/typo3conf/ext/' . $this->render->getExtensionName() . '/Classes/ContentElement/' . $this->render->getName() . '.php';
        if (!file_exists($filename)) {
            $view = clone $this->view;
            $view->setTemplatePathAndFilename(
                GeneralUtility::getFileAbsFileName(
                    'EXT:typo3_element_registry_cli/Resources/Private/Templates/ContentElementClassTemplate.html'
                )
            );
            $view->assignMultiple([
                'vendor' => $this->render->getVendor(),
                'name' => $this->render->getName(),
                'extensionName' => $this->render->getExtensionNameSpaceFormat()
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
