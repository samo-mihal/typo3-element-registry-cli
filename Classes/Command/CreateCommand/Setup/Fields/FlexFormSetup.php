<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\FlexFormFieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Run\QuestionsRun;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\AdvanceFieldsSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\RunCreateElementCommand;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields\FlexForm\FlexFormFieldsSetup;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FlexForm
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields
 */
class FlexFormSetup
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

        $inlineKeysOfInlineFields = AdvanceFieldsSetup::getArrayKeyOfAdvanceFields();
        AdvanceFieldsSetup::setArrayKeyOfAdvanceFields(AdvanceFieldsSetup::getArrayKeyOfAdvanceFields() + 1);

        $flexFormTitle = 'NoDefined';

        $this->setFlexFormItem($flexFormName . ';' . $inlineKeysOfInlineFields . ';' . $flexFormTitle . '*');
        $this->run->getOutput()->writeln(QuestionsRun::getColoredDeepLevel() . 'Create at least one flexForm field.');

        $editedRunSetup = $this->run;
        $editedRunSetup->setFieldTypes(
            GeneralUtility::makeInstance(FlexFormFieldTypesConfig::class)->getFlexFormFieldTypes()
        );
        $newInlineFields = new FlexFormFieldsSetup($editedRunSetup);
        $newInlineFields->createField();
        $flexFormFields = AdvanceFieldsSetup::getAdvanceFields() + [$inlineKeysOfInlineFields => $newInlineFields->getFields()];

        AdvanceFieldsSetup::setAdvanceFields(
            $flexFormFields
        );
    }
}
