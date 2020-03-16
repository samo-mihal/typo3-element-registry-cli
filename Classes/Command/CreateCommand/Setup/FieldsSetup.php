<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\Typo3FieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Run\QuestionsRun;
use Digitalwerk\Typo3ElementRegistryCli\Command\RunCreateElementCommand;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields\FlexFormSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields\InlineSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields\ItemsSetup;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FieldsSetup
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup
 */
class FieldsSetup
{
    /**
     * @var RunCreateElementCommand
     */
    protected $run = null;

    /**
     * FieldsSetup constructor.
     * @param RunCreateElementCommand $run
     */
    public function __construct(RunCreateElementCommand $run)
    {
        $this->run = $run;
    }

    /**
     * @var string
     */
    protected $fields = '';

    /**
     * @return string
     */
    public function getFields(): string
    {
        return $this->fields;
    }

    /**
     * @param string $fields
     */
    public function setFields(string $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function createField()
    {
        $fieldName = $this->run->getQuestions()->askFieldName();
        $fieldType = $this->run->getQuestions()->askFieldType();
        $fieldTitle = $this->run->getQuestions()->askFieldTitle();

        if (strlen(QuestionsRun::getRawDeepLevel()) - strlen(QuestionsRun::DEEP_LEVEL_SPACES) === strlen(QuestionsRun::DEEP_LEVEL_SPACES)) {
            $table = $this->run->getTable();
            $fieldTypes = GeneralUtility::makeInstance(Typo3FieldTypesConfig::class)->getTCAFieldTypes($table)[$table];
            $this->run->setFieldTypes($fieldTypes);
        } else {
            $fieldTypes = $this->run->getFieldTypes();
        }

        if ($fieldTypes[$fieldType]['TCAItemsAllowed']) {
            QuestionsRun::setDeepLevelDown();
            $this->run->getOutput()->writeln(QuestionsRun::getColoredDeepLevel() . 'Create at least one item.');
            $itemsSetup = new ItemsSetup($this->run);
            $itemsSetup->createItem();
            $field = $fieldName . ',' . $fieldType . ',' . $fieldTitle . ',' . $itemsSetup->getItems() . '/';
        } elseif ($fieldTypes[$fieldType]['inlineFieldsAllowed']) {
            QuestionsRun::setDeepLevelDown();
            $this->run->getOutput()->writeln(QuestionsRun::getColoredDeepLevel() . 'Configure inline field.');
            $inlineSetup = new InlineSetup($this->run);
            $inlineSetup->createInlineItem();
            $field = $fieldName . ',' . $fieldType . ',' . $fieldTitle . ',' . $inlineSetup->getInlineItem() . '/';
        } elseif ($fieldTypes[$fieldType]['FlexFormItemsAllowed']) {
            QuestionsRun::setDeepLevelDown();
            $this->run->getOutput()->writeln(QuestionsRun::getColoredDeepLevel() . 'Configure flexForm field.');
            $flexFormSetup = new FlexFormSetup($this->run);
            $flexFormSetup->createFlexForm();
            $field = $fieldName . ',' . $fieldType . ',' . $fieldTitle . ',' . $flexFormSetup->getFlexFormItem() . '/';
        } else {
            $field = $fieldName . ',' . $fieldType . ',' . $fieldTitle . '/';
        }

        $this->setFields($this->getFields() . $field);
        if ($this->run->getQuestions()->needCreateMoreFields()) {
            $this->createField();
        } else {
            QuestionsRun::setDeepLevelUp();
        }
    }
}
