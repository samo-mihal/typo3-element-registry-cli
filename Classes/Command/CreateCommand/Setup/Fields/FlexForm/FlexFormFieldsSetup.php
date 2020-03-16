<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields\FlexForm;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Run\QuestionsRun;
use Digitalwerk\Typo3ElementRegistryCli\Command\RunCreateElementCommand;

/**
 * Class FlexFormFieldsSetup
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields\FlexForm
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
        $fieldName = $this->run->getQuestions()->askFieldName();
        $fieldType = $this->run->getQuestions()->askFieldType();
        $fieldTitle = $this->run->getQuestions()->askFieldTitle();

        $field = $fieldName . ',' . $fieldType . ',' . $fieldTitle . '/';

        $this->setFields($this->getFields() . $field);

        if ($this->run->getQuestions()->needCreateMoreFields()) {
            $this->createField();
        } else {
            QuestionsRun::setDeepLevelUp();
        }
    }
}
