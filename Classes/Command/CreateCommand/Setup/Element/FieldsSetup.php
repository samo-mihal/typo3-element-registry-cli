<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\Typo3FieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\ElementSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element\Fields\FlexFormSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element\Fields\InlineSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element\Fields\ItemsSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\QuestionsSetup;
use Digitalwerk\Typo3ElementRegistryCli\Utility\FieldsCreateCommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class FieldsSetup
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element
 */
class FieldsSetup
{
    /**
     * @var ElementSetup
     */
    protected $elementSetup = null;

    /**
     * @var FieldsCreateCommandUtility
     */
    protected $fieldsCreateCommandUtility = null;

    /**
     * FieldsSetup constructor.
     * @param ElementSetup $elementSetup
     */
    public function __construct(ElementSetup $elementSetup)
    {
        $this->elementSetup = $elementSetup;
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
        $fieldName = $this->elementSetup->getQuestions()->askFieldName();
        $fieldType = $this->elementSetup->getQuestions()->askFieldType();
        $fieldTitle = $this->elementSetup->getQuestions()->askFieldTitle();
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

        if (strlen(QuestionsSetup::getRawDeepLevel()) - strlen(QuestionsSetup::DEEP_LEVEL_SPACES) === strlen(QuestionsSetup::DEEP_LEVEL_SPACES)) {
            $table = $this->elementSetup->getElementObject()->getTable();
            $fieldTypes = GeneralUtility::makeInstance(Typo3FieldTypesConfig::class)->getTCAFieldTypes($table)[$table];
            $this->elementSetup->setFieldTypes($fieldTypes);
        } else {
            $fieldTypes = $this->elementSetup->getFieldTypes();
        }

        if ($fieldTypes[$fieldType]['TCAItemsAllowed']) {
            QuestionsSetup::setDeepLevelDown();
            $this->elementSetup->getOutput()->writeln(QuestionsSetup::getColoredDeepLevel() . 'Create at least one item.');
            $itemsSetup = new ItemsSetup($this->elementSetup);
            $itemsSetup->createItem();
            $field->setItems($itemsSetup->getItems());
        } elseif ($fieldTypes[$fieldType]['inlineFieldsAllowed']) {
            QuestionsSetup::setDeepLevelDown();
            $this->elementSetup->getOutput()->writeln(QuestionsSetup::getColoredDeepLevel() . 'Configure inline field.');
            $inlineSetup = new InlineSetup($this->elementSetup);
            $inlineSetup->createInlineItems($field);
            $field->setItems($inlineSetup->getInlineItems());
        } elseif ($fieldTypes[$fieldType]['FlexFormItemsAllowed']) {
            QuestionsSetup::setDeepLevelDown();
            $this->elementSetup->getOutput()->writeln(QuestionsSetup::getColoredDeepLevel() . 'Configure flexForm field.');
            $flexFormSetup = new FlexFormSetup($this->elementSetup);
            $flexFormSetup->createFlexForm();
            $field->setItems($flexFormSetup->getFlexFormItems());
        }

        $field->setSqlDataType($this->fieldsCreateCommandUtility->getFieldSQLDataType($table, $fieldType, $field));
        $fields = $this->getFields();
        $fields->attach($field);
        $this->setFields($fields);
        if ($this->elementSetup->getQuestions()->needCreateMoreFields()) {
            $this->createField($table);
        } else {
            QuestionsSetup::setDeepLevelUp();
        }
    }
}
