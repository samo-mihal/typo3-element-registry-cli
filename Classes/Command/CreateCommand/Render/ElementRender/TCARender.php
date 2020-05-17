<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TCARender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
class TCARender extends AbstractRender
{
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
     * @var string
     */
    protected $fieldsSpacesInTCAColumn = '';

    /**
     * @var string
     */
    protected $table = '';

    /**
     * TCARender constructor.
     * @param ElementRender $elementRender
     */
    public function __construct(ElementRender $elementRender)
    {
        parent::__construct($elementRender);
        $this->table = $elementRender->getElement()->getTable();
        $this->fieldsRender = GeneralUtility::makeInstance(FieldsRender::class, $elementRender);
        $this->overrideFilename = 'public/typo3conf/ext/' . $elementRender->getElement()->getExtensionName() . '/Configuration/TCA/Overrides/' . $this->table . '_' . $elementRender->getElement()->getTCANameFromModelPath() . '.php';
        $this->filename = 'public/typo3conf/ext/' . $elementRender->getElement()->getExtensionName() . '/Configuration/TCA/tx_' . strtolower($elementRender->getElement()->getExtensionNameSpaceFormat()) . '_domain_model_' . $elementRender->getElement()->getTCANameFromModelPath() . '.php';
        $this->fieldsSpacesInTCAColumn = $elementRender->getElement()->getFieldsSpacesInTcaColumn();
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
            'name' => $this->elementRender->getElement()->getName(),
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
        if (!file_exists($this->overrideFilename) && $this->fields && !$this->elementRender->getElement()->areAllFieldsDefault()) {
            $view = clone $this->view;
            $view->setTemplatePathAndFilename(
                GeneralUtility::getFileAbsFileName(
                    'EXT:typo3_element_registry_cli/Resources/Private/Templates/TCA/TCAContentElementTemplate.html'
                )
            );
            $this->view->assignMultiple([
                'name' => $this->elementRender->getElement()->getName(),
                'table' => $this->table,
            ]);
            file_put_contents($this->overrideFilename, $view->render());
        }
        $fieldsToColumn = $this->fieldsRender->fieldsToColumn();
        if ($fieldsToColumn && !$this->elementRender->getElement()->areAllFieldsDefault()) {
            GeneralCreateCommandUtility::importStringInToFileAfterString(
                $this->overrideFilename,
                [$this->fieldsSpacesInTCAColumn . $fieldsToColumn . "\n"],
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
                'pathToModel' => '\\' . $this->elementRender->getElement()->getModelNamespace(),
                'table' => $this->table,
                'name' => $this->elementRender->getElement()->getName(),
            ]);

            file_put_contents($this->overrideFilename, $view->render());
        }

        $fieldsToColumn = $this->fieldsRender->fieldsToColumn();
        if ($fieldsToColumn) {
            GeneralCreateCommandUtility::importStringInToFileAfterString(
                $this->overrideFilename,
                [$this->fieldsSpacesInTCAColumn . $fieldsToColumn . "\n"],
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
                '\'types\' => [',
                2,
                '=> \'type,',
                0,
                $fieldsToType . ','
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
            $name = $this->elementRender->getElement()->getName();

            $view = clone $this->view;
            $view->setTemplatePathAndFilename(
                GeneralUtility::getFileAbsFileName(
                    'EXT:typo3_element_registry_cli/Resources/Private/Templates/TCA/TCAPageTypeTemplate.html'
                )
            );
            $this->view->assignMultiple([
                'pageTypeDoktypeFunction' => '\\' . $this->elementRender->getElement()->getVendor() . '\\' . $this->elementRender->getElement()->getExtensionNameSpaceFormat() . '\Domain\Model\\' . $name . '::getDoktype()',
                'table' => $this->table,
                'name' => $name,
                'extensionName' => $this->elementRender->getElement()->getExtensionName(),
                'doktype' => $this->elementRender->getElement()->getDoktype(),
                'extensionNameSpaceFormat' => $this->elementRender->getElement()->getExtensionNameSpaceFormat(),
                'vendor' => $this->elementRender->getElement()->getVendor()
            ]);

            file_put_contents($this->overrideFilename, $view->render());
        }

        $fieldsToColumn = $this->fieldsRender->fieldsToColumn();
        if ($fieldsToColumn) {
            GeneralCreateCommandUtility::importStringInToFileAfterString(
                $this->overrideFilename,
                [$this->fieldsSpacesInTCAColumn . $fieldsToColumn . "\n"],
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
                '\'palettes\' => [',
                3,
                '=> \'',
                0,
                $fieldsToPalette . ','
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
            $name = $this->elementRender->getElement()->getName();

            $view = clone $this->view;
            $view->setTemplatePathAndFilename(
                GeneralUtility::getFileAbsFileName(
                    'EXT:typo3_element_registry_cli/Resources/Private/Templates/TCA/TCARecordTemplate.html'
                )
            );
            $this->view->assignMultiple([
                'table' => $this->table,
                'name' => $name,
                'extensionName' => $this->elementRender->getElement()->getExtensionName(),
            ]);

            file_put_contents($this->filename, $view->render());
        }

        $fieldsToColumn = $this->fieldsRender->fieldsToColumn();
        if ($fieldsToColumn) {
            GeneralCreateCommandUtility::importStringInToFileAfterString(
                $this->filename,
                [$this->fieldsSpacesInTCAColumn . $fieldsToColumn . "\n"],
                '\'endtime\' => [',
                16
            );
        }
        $fieldsToPalette = $this->fieldsRender->fieldsToPalette();
        if ($fieldsToPalette) {
            GeneralCreateCommandUtility::insertStringToFileInlineAfter(
                $this->filename,
                '\'palettes\' => [',
                2,
                '=> \'',
                0,
                $fieldsToPalette . ','
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
                'name' => $this->elementRender->getElement()->getName(),
                'staticName' => $this->elementRender->getElement()->getStaticName(),
                'extensionName' => $this->elementRender->getElement()->getExtensionName(),
                'extensionNameInNameSpace' => $this->elementRender->getElement()->getExtensionNameSpaceFormat(),
            ]);

            file_put_contents($this->filename, $view->render());
        }

        $fieldsToColumn = $this->fieldsRender->fieldsToColumn();
        if ($fieldsToColumn) {
            GeneralCreateCommandUtility::importStringInToFileAfterString(
                $this->filename,
                [$this->fieldsSpacesInTCAColumn . $fieldsToColumn . "\n"],
                '\'endtime\' => [',
                16
            );
        }
        $fieldsToPalette = $this->fieldsRender->fieldsToPalette();
        if ($fieldsToPalette) {
            GeneralCreateCommandUtility::insertStringToFileInlineAfter(
                $this->filename,
                '\'palettes\' => [',
                2,
                '=> \'',
                0,
                $fieldsToPalette . ','
            );
        }
    }
}
