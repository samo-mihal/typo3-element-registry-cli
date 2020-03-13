<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields\FlexForm;

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
        $fieldName = $this->run->askFieldName();
        $fieldType = $this->run->askFieldType();
        $fieldTitle = $this->run->askFieldTitle();

        $field = $fieldName . ',' . $fieldType . ',' . $fieldTitle . '/';

        $this->setFields($this->getFields() . $field);

        if ($this->run->needCreateMoreFields()) {
            $this->createField();
        } else {
            RunCreateElementCommand::setDeepLevel(substr(RunCreateElementCommand::getRawDeepLevel(), 0, -strlen(RunCreateElementCommand::DEEP_LEVEL_SPACES)));
        }
    }
}
