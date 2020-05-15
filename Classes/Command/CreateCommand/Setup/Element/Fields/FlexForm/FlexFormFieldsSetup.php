<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element\Fields\FlexForm;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\ElementSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\QuestionsSetup;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class FlexFormFieldsSetup
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element\Fields\FlexForm
 */
class FlexFormFieldsSetup
{
    /**
     * @var ElementSetup
     */
    protected $elementSetup = null;

    /**
     * @var ObjectStorage
     */
    protected $fields = '';

    /**
     * FlexFormFieldsSetup constructor.
     * @param ElementSetup $elementSetup
     */
    public function __construct(ElementSetup $elementSetup)
    {
        $this->elementSetup = $elementSetup;
        $this->fields = GeneralUtility::makeInstance(ObjectStorage::class);
    }

    /**
     * @return ObjectStorage
     */
    public function getFields(): ObjectStorage
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

    public function createField()
    {
        $fieldName = $this->elementSetup->getQuestions()->askFieldName();
        $fieldType = $this->elementSetup->getQuestions()->askFieldType();
        $fieldTitle = $this->elementSetup->getQuestions()->askFieldTitle();
        $field = new FieldObject();
        $field->setName($fieldName);
        $field->setType($fieldType);
        $field->setTitle($fieldTitle);

        $fields = $this->getFields();
        $fields->attach($field);
        $this->setFields($fields);

        if ($this->elementSetup->getQuestions()->needCreateMoreFields()) {
            $this->createField();
        } else {
            QuestionsSetup::setDeepLevelUp();
        }
    }
}
