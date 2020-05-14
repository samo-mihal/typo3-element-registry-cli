<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\Typo3FieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Run\QuestionsRun;
use Digitalwerk\Typo3ElementRegistryCli\Command\RunCreateElementCommand;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields\FlexFormSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields\InlineSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields\ItemsSetup;
use Digitalwerk\Typo3ElementRegistryCli\Utility\FieldsCreateCommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

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
     * @var FieldsCreateCommandUtility
     */
    protected $fieldsCreateCommandUtility = null;

    /**
     * FieldsSetup constructor.
     * @param RunCreateElementCommand $run
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function __construct(RunCreateElementCommand $run)
    {
        $this->run = $run;
        $this->fieldsCreateCommandUtility = GeneralUtility::makeInstance(FieldsCreateCommandUtility::class);
        $this->fields = GeneralUtility::makeInstance(ObjectStorage::class);
    }

    /**
     * @var ObjectStorage<FieldObject>
     */
    protected $fields = null;

    /**
     * @return ObjectStorage
     */
    public function getFields(): ? ObjectStorage
    {
        return $this->fields;
    }

    /**
     * @param ObjectStorage $fields
     */
    public function setFields(ObjectStorage $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @param $table
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function createField($table)
    {
        $this->fieldsCreateCommandUtility->inicializeTCAFieldTypes($table);
        $fieldName = $this->run->getQuestions()->askFieldName();
        $fieldType = $this->run->getQuestions()->askFieldType();
        $fieldTitle = $this->run->getQuestions()->askFieldTitle();
        $field = new FieldObject();
        $field->setName($fieldName);
        $field->setType($fieldType);
        $field->setTitle($fieldTitle);
        $field->setDefault($this->fieldsCreateCommandUtility->isFieldTypeDefault($table, $fieldType));
        $field->setExist($this->fieldsCreateCommandUtility->isFieldTypeExist($table, $fieldType));
        $field->setDefaultTitle($this->fieldsCreateCommandUtility->getFieldDefaultTitle($table, $fieldType));
        $field->setImportClasses($this->fieldsCreateCommandUtility->getFieldImportClasses($table, $fieldType));
        $field->setTCAItemsAllowed($this->fieldsCreateCommandUtility->isFieldTCAItemsAllowed($table, $fieldType));
        $field->setFlexFormItemsAllowed($this->fieldsCreateCommandUtility->isFlexFormTCAItemsAllowed($table, $fieldType));
        $field->setInlineItemsAllowed($this->fieldsCreateCommandUtility->isFieldInlineItemsAllowed($table, $fieldType));
        if ($this->fieldsCreateCommandUtility->hasNotFieldModel($table, $fieldType)) {
            $field->setHasModel(false);
        }

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
            $field->setItems($itemsSetup->getItems());
        } elseif ($fieldTypes[$fieldType]['inlineFieldsAllowed']) {
            QuestionsRun::setDeepLevelDown();
            $this->run->getOutput()->writeln(QuestionsRun::getColoredDeepLevel() . 'Configure inline field.');
            $inlineSetup = new InlineSetup($this->run);
            $inlineSetup->createInlineItems($field);
            $field->setItems($inlineSetup->getInlineItems());
        } elseif ($fieldTypes[$fieldType]['FlexFormItemsAllowed']) {
            QuestionsRun::setDeepLevelDown();
            $this->run->getOutput()->writeln(QuestionsRun::getColoredDeepLevel() . 'Configure flexForm field.');
            $flexFormSetup = new FlexFormSetup($this->run);
            $flexFormSetup->createFlexForm();
            $field->setItems($flexFormSetup->getFlexFormItems());
        }

        $field->setSqlDataType($this->fieldsCreateCommandUtility->getFieldSQLDataType($table, $fieldType, $field));
        $fields = $this->getFields();
        $fields->attach($field);
        $this->setFields($fields);
        if ($this->run->getQuestions()->needCreateMoreFields()) {
            $this->createField($table);
        } else {
            QuestionsRun::setDeepLevelUp();
        }
    }
}
