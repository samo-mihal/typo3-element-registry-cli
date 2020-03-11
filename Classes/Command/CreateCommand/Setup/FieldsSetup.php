<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\Typo3FieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RunCreateCommand;
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
     * @var RunCreateCommand
     */
    protected $run = null;

    /**
     * FieldsSetup constructor.
     * @param RunCreateCommand $run
     */
    public function __construct(RunCreateCommand $run)
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

    public function createField()
    {
        $fieldName = $this->run->askFieldName();
        $fieldType = $this->run->askFieldType();
        $fieldTitle = $this->run->askFieldTitle();

        if (strlen(RunCreateCommand::getRawDeepLevel()) - strlen(RunCreateCommand::DEEP_LEVEL_SPACES) === strlen(RunCreateCommand::DEEP_LEVEL_SPACES)) {
            $table = $this->run->getTable();
            $fieldTypes = GeneralUtility::makeInstance(Typo3FieldTypesConfig::class)->getTCAFieldTypes($table)[$table];
            $this->run->setFieldTypes($fieldTypes);
        } else {
            $fieldTypes = $this->run->getFieldTypes();
        }

        if ($fieldTypes[$fieldType]['TCAItemsAllowed']) {
            $this->goDeepLevel();
            $this->run->getOutput()->writeln(RunCreateCommand::getColoredDeepLevel() . 'Create at least one item.');
            $itemsSetup = new ItemsSetup($this->run);
            $itemsSetup->createItem();
            $field = $fieldName . ',' . $fieldType . ',' . $fieldTitle . ',' . $itemsSetup->getItems() . '/';
        } elseif ($fieldTypes[$fieldType]['inlineFieldsAllowed']) {
            $this->goDeepLevel();
            $this->run->getOutput()->writeln(RunCreateCommand::getColoredDeepLevel() . 'Configure inline field.');
            $inlineSetup = new InlineSetup($this->run);
            $inlineSetup->createInlineItem();
            $field = $fieldName . ',' . $fieldType . ',' . $fieldTitle . ',' . $inlineSetup->getInlineItem() . '/';
        } elseif ($fieldTypes[$fieldType]['FlexFormItemsAllowed']) {
            $this->goDeepLevel();
            $this->run->getOutput()->writeln(RunCreateCommand::getColoredDeepLevel() . 'Configure flexForm field.');
            $flexFormSetup = new FlexFormSetup($this->run);
            $flexFormSetup->createFlexForm();
            $field = $fieldName . ',' . $fieldType . ',' . $fieldTitle . ',' . $flexFormSetup->getFlexFormItem() . '/';
        } else {
            $field = $fieldName . ',' . $fieldType . ',' . $fieldTitle . '/';
        }

        $this->setFields($this->getFields() . $field);
        if ($this->run->needCreateMoreFields()) {
            $this->createField();
        } else {
            RunCreateCommand::setDeepLevel(substr(RunCreateCommand::getRawDeepLevel(), 0, -strlen(RunCreateCommand::DEEP_LEVEL_SPACES)));
        }
    }


    public function goDeepLevel() {
        RunCreateCommand::setDeepLevel(RunCreateCommand::getRawDeepLevel() . RunCreateCommand::DEEP_LEVEL_SPACES);
    }
}
