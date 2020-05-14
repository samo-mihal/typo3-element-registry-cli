<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields\FlexForm;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Run\QuestionsRun;
use Digitalwerk\Typo3ElementRegistryCli\Command\RunCreateElementCommand;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class FlexFormFieldsSetup
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element\FlexForm
 */
class FlexFormFieldsSetup
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
     * @var ObjectStorage
     */
    protected $fields = '';

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
        $fieldName = $this->run->getQuestions()->askFieldName();
        $fieldType = $this->run->getQuestions()->askFieldType();
        $fieldTitle = $this->run->getQuestions()->askFieldTitle();
        $field = new FieldObject();
        $field->setName($fieldName);
        $field->setType($fieldType);
        $field->setTitle($fieldTitle);

        $fields = $this->getFields();
        $fields->attach($field);
        $this->setFields($fields);

        if ($this->run->getQuestions()->needCreateMoreFields()) {
            $this->createField();
        } else {
            QuestionsRun::setDeepLevelUp();
        }
    }
}
