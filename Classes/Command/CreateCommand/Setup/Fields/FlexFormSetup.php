<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\FlexFormFieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RunCreateCommand;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields\FlexForm\FlexFormFieldsSetup;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FlexForm
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields
 */
class FlexFormSetup
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
    protected $flexFormItem = '';

    /**
     * @return string
     */
    public function getFlexFormItem(): string
    {
        return $this->flexFormItem;
    }

    /**
     * @param string $flexFormItem
     */
    public function setFlexFormItem(string $flexFormItem): void
    {
        $this->flexFormItem = $flexFormItem;
    }

    /**
     * @return string
     */
    public function createFlexForm()
    {
        $flexFormName = 'NoDefined';

        $inlineKeysOfInlineFields = RunCreateCommand::getArrayKeyOfInlineFields();
        RunCreateCommand::setArrayKeyOfInlineFields(RunCreateCommand::getArrayKeyOfInlineFields() + 1);

        $flexFormTitle = 'NoDefined';

        $this->setFlexFormItem($flexFormName . ';' . $inlineKeysOfInlineFields . ';' . $flexFormTitle . '*');
        $this->run->getOutput()->writeln(RunCreateCommand::getColoredDeepLevel() . 'Create at least one flexForm field.');

        $editedRunSetup = $this->run;
        $editedRunSetup->setFieldTypes(
            GeneralUtility::makeInstance(FlexFormFieldTypesConfig::class)->getFlexFormFieldTypes()
        );
        $newInlineFields = new FlexFormFieldsSetup($editedRunSetup);
        $newInlineFields->createField();
        $inlineFields = RunCreateCommand::getInlineFields() + [$inlineKeysOfInlineFields => $newInlineFields->getFields()];

        RunCreateCommand::setInlineFields(
            $inlineFields
        );
    }
}
