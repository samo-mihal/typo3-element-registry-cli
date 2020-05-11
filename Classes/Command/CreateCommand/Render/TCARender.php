<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class TCA
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render
 */
class TCARender
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
     * @var string
     */
    protected $overrideFilename = '';

    /**
     * @var string
     */
    protected $filename = '';

    /**
     * @var StandaloneView
     */
    protected $view = null;

    /**
     * @var string
     */
    protected $table = '';

    /**
     * TCA constructor.
     * @param RenderCreateCommand $render
     */
    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
        $this->table = $render->getTable();
        $this->fieldsRender = GeneralUtility::makeInstance(FieldsRender::class, $render);
        $this->overrideFilename = 'public/typo3conf/ext/' . $render->getExtensionName() . '/Configuration/TCA/Overrides/' . $this->table . '_' . $render->getTcaRelativePath() . '.php';
        $this->filename = 'public/typo3conf/ext/' . $render->getExtensionName() . '/Configuration/TCA/tx_' . strtolower($render->getExtensionNameSpaceFormat()) . '_domain_model_' . $render->getTcaRelativePath() . '.php';
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
    }

    /**
     * @param $fieldsToColumn
     * @return string
     */
    public function columnsTemplate($fieldsToColumn) {
        $view = clone $this->view;
        $view->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName(
                'EXT:typo3_element_registry_cli/Resources/Private/Templates/TCA/TCAColumnTemplate.html'
            )
        );
        $this->view->assignMultiple([
            'name' => $this->render->getName(),
            'table' => $this->table,
            'fieldsToColumn' => $fieldsToColumn
        ]);
        return $view->render();
    }

    /**
     * @param $fieldsToColumnsOverrides
     * @return string
     */
    public function columnsOverridesTemplate($fieldsToColumnsOverrides) {
        $view = clone $this->view;
        $view->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName(
                'EXT:typo3_element_registry_cli/Resources/Private/Templates/TCA/TCAColumnsOverridesTemplate.html'
            )
        );
        $this->view->assignMultiple([
            'fieldsToColumnsOverrides' => $fieldsToColumnsOverrides
        ]);
        return $view->render();
    }

    /**
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function contentElementTemplate()
    {
        if (!file_exists($this->overrideFilename) && $this->render->getFields() && !$this->render->getFields()->areDefault()) {
            $view = clone $this->view;
            $view->setTemplatePathAndFilename(
                GeneralUtility::getFileAbsFileName(
                    'EXT:typo3_element_registry_cli/Resources/Private/Templates/TCA/TCAContentElementTemplate.html'
                )
            );
            $this->view->assignMultiple([
                'name' => $this->render->getName(),
                'table' => $this->table,
            ]);
            file_put_contents($this->overrideFilename, $view->render());
        }
        $fieldsToColumn = $this->fieldsRender->fieldsToColumn();
        if ($fieldsToColumn && !$this->render->getFields()->areDefault()) {
            GeneralCreateCommandUtility::importStringInToFileAfterString(
                $this->overrideFilename,
                [$this->render->getFields()->getSpacesInTcaColumn() . $fieldsToColumn . "\n"],
                '* ' . $this->table . ' new fields',
                2,
                [
                    'newLines' => $this->columnsTemplate($fieldsToColumn),
                    'universalStringInFile' => 'defined(\'TYPO3_MODE\') or die();',
                    'linesAfterSpecificString' => 0
                ]
            );
        }
    }

    /**
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function inlineTemplate()
    {
        if (!file_exists($this->overrideFilename)) {
            $view = clone $this->view;
            $view->setTemplatePathAndFilename(
                GeneralUtility::getFileAbsFileName(
                    'EXT:typo3_element_registry_cli/Resources/Private/Templates/TCA/TCAInlineTemplate.html'
                )
            );
            $this->view->assignMultiple([
                'pathToModel' => '\\' . $this->render->getModelNamespace(),
                'table' => $this->table,
                'name' => $this->render->getName(),
            ]);

            file_put_contents($this->overrideFilename, $view->render());
        }

        $fieldsToColumn = $this->fieldsRender->fieldsToColumn();
        if ($fieldsToColumn) {
            GeneralCreateCommandUtility::importStringInToFileAfterString(
                $this->overrideFilename,
                [$this->render->getFields()->getSpacesInTcaColumn() . $fieldsToColumn . "\n"],
                '* ' . $this->table . ' new fields',
                2,
                [
                    'newLines' => $this->columnsTemplate($fieldsToColumn),
                    'universalStringInFile' => '];',
                    'linesAfterSpecificString' => 2
                ]
            );
        }
        $fieldsToType = $this->fieldsRender->fieldsToType();
        if ($fieldsToType) {
            GeneralCreateCommandUtility::insertStringToFileInlineAfter(
                $this->overrideFilename,
                '\'showitem\' => \'type,',
                '=> \'type,',
                0,
                $fieldsToType
            );
        }
        $fieldsToColumnOverrides = $this->fieldsRender->fieldsToColumnsOverrides();
        if ($fieldsToColumnOverrides) {
            GeneralCreateCommandUtility::importStringInToFileAfterString(
                $this->overrideFilename,
                [$fieldsToColumnOverrides . "\n"],
                '\'columnsOverrides\' => [',
                0,
                [
                    'newLines' => $this->columnsOverridesTemplate($fieldsToColumnOverrides),
                    'universalStringInFile' => "--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, hidden, starttime, endtime, sys_language_uid, l10n_parent, l10n_diffsource',",
                    'linesAfterSpecificString' => 0
                ]
            );
        }
    }

    /**
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function pageTypeTemplate()
    {
        if (!file_exists($this->overrideFilename)) {
            $name = $this->render->getName();

            $view = clone $this->view;
            $view->setTemplatePathAndFilename(
                GeneralUtility::getFileAbsFileName(
                    'EXT:typo3_element_registry_cli/Resources/Private/Templates/TCA/TCAPageTypeTemplate.html'
                )
            );
            $this->view->assignMultiple([
                'pageTypeDoktypeFunction' => '\\' . $this->render->getVendor() . '\\' . $this->render->getExtensionNameSpaceFormat() . '\Domain\Model\\' . $name . '::getDoktype()',
                'table' => $this->table,
                'name' => $name,
                'extensionName' => $this->render->getExtensionName(),
                'doktype' => $this->render->getDoktype(),
                'extensionNameSpaceFormat' => $this->render->getExtensionNameSpaceFormat(),
                'vendor' => $this->render->getVendor()
            ]);

            file_put_contents($this->overrideFilename, $view->render());
        }

        $fieldsToColumn = $this->fieldsRender->fieldsToColumn();
        if ($fieldsToColumn) {
            GeneralCreateCommandUtility::importStringInToFileAfterString(
                $this->overrideFilename,
                [$this->render->getFields()->getSpacesInTcaColumn() . $fieldsToColumn . "\n"],
                '* ' . $this->table . ' new fields',
                2,
                [
                    'newLines' => $this->columnsTemplate($fieldsToColumn) . "\n",
                    'universalStringInFile' => '];',
                    'linesAfterSpecificString' => 1
                ]
            );
        }
        $fieldsToPalette = $this->fieldsRender->fieldsToPalette();
        if ($fieldsToPalette) {
            GeneralCreateCommandUtility::insertStringToFileInlineAfter(
                $this->overrideFilename,
                '\'showitem\' => \'\'',
                '=> \'',
                0,
                $fieldsToPalette
            );
        }
        $fieldsToColumnOverrides = $this->fieldsRender->fieldsToColumnsOverrides();
        if ($fieldsToColumnOverrides) {
            GeneralCreateCommandUtility::importStringInToFileAfterString(
                $this->overrideFilename,
                [$fieldsToColumnOverrides . "\n"],
                '\'columnsOverrides\' => [',
                0,
                [
                    'newLines' => $this->columnsOverridesTemplate($fieldsToColumnOverrides),
                    'universalStringInFile' => '\'types\' => [',
                    'linesAfterSpecificString' => 1
                ]
            );
        }
    }

    /**
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function recordTemplate()
    {
        if (!file_exists($this->filename)) {
            $name = $this->render->getName();

            $view = clone $this->view;
            $view->setTemplatePathAndFilename(
                GeneralUtility::getFileAbsFileName(
                    'EXT:typo3_element_registry_cli/Resources/Private/Templates/TCA/TCARecordTemplate.html'
                )
            );
            $this->view->assignMultiple([
                'table' => $this->table,
                'name' => $name,
                'extensionName' => $this->render->getExtensionName(),
            ]);

            file_put_contents($this->filename, $view->render());
        }

        $fieldsToColumn = $this->fieldsRender->fieldsToColumn();
        if ($fieldsToColumn) {
            GeneralCreateCommandUtility::importStringInToFileAfterString(
                $this->filename,
                [$this->render->getFields()->getSpacesInTcaColumn() . $fieldsToColumn . "\n"],
                '\'endtime\' => [',
                16
            );
        }
        $fieldsToPalette = $this->fieldsRender->fieldsToPalette();
        if ($fieldsToPalette) {
            GeneralCreateCommandUtility::insertStringToFileInlineAfter(
                $this->filename,
                '\'showitem\' => \'\',',
                '=> \'',
                0,
                $fieldsToPalette
            );
        }
    }

    /**
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function newInlineTemplate()
    {
        if (!file_exists($this->filename)) {
            $view = clone $this->view;
            $view->setTemplatePathAndFilename(
                GeneralUtility::getFileAbsFileName(
                    'EXT:typo3_element_registry_cli/Resources/Private/Templates/TCA/TCANewInlineTemplate.html'
                )
            );
            $this->view->assignMultiple([
                'table' => $this->table,
                'name' => $this->render->getName(),
                'staticName' => $this->render->getStaticName(),
                'extensionName' => $this->render->getExtensionName(),
                'extensionNameInNameSpace' => $this->render->getExtensionNameSpaceFormat(),
            ]);

            file_put_contents($this->filename, $view->render());
        }

        $fieldsToColumn = $this->fieldsRender->fieldsToColumn();
        if ($fieldsToColumn) {
            GeneralCreateCommandUtility::importStringInToFileAfterString(
                $this->filename,
                [$this->render->getFields()->getSpacesInTcaColumn() . $fieldsToColumn . "\n"],
                '\'endtime\' => [',
                16
            );
        }
        $fieldsToPalette = $this->fieldsRender->fieldsToPalette();
        if ($fieldsToPalette) {
            GeneralCreateCommandUtility::insertStringToFileInlineAfter(
                $this->filename,
                '\'showitem\' => \'\',',
                '=> \'',
                0,
                $fieldsToPalette
            );
        }
    }
}
