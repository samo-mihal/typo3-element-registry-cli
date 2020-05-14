<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\Typo3FieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\Field\ItemObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Run\QuestionsRun;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\AdvanceFieldsSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\RunCreateElementCommand;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\FieldsSetup;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class InlineSetup
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element
 */
class InlineSetup
{
    /**
     * @var string
     */
    protected $inlineTable = 'tx_contentelementregistry_domain_model_relation';

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
        $this->inlineItems = GeneralUtility::makeInstance(ObjectStorage::class);
    }

    /**
     * @var ObjectStorage
     */
    protected $inlineItems = null;

    /**
     * @return ObjectStorage|null
     */
    public function getInlineItems(): ? ObjectStorage
    {
        return $this->inlineItems;
    }

    /**
     * @return string
     */
    public function getInlineTable(): string
    {
        return $this->inlineTable;
    }

    /**
     * @param ObjectStorage $inlineItems
     */
    public function setInlineItems(ObjectStorage $inlineItems): void
    {
        $this->inlineItems = $inlineItems;
    }

    /**
     * @param FieldObject $field
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function createInlineItems(FieldObject $field)
    {
        $inlineKeysOfInlineFields = AdvanceFieldsSetup::getArrayKeyOfAdvanceFields();
        AdvanceFieldsSetup::setArrayKeyOfAdvanceFields(AdvanceFieldsSetup::getArrayKeyOfAdvanceFields() + 1);

        $item = new ItemObject();
        $item->setName($this->run->getQuestions()->askInlineClassName());
        $item->setValue($inlineKeysOfInlineFields);
        $item->setTitle($this->run->getQuestions()->askInlineTitle());

        $items = $this->getInlineItems();
        $items->attach($item);
        $this->setInlineItems($items);

        $this->run->getOutput()->writeln(QuestionsRun::getColoredDeepLevel() . 'Create at least one inline field.');

        $table = $field->isDefault() ? $this->getInlineTable() : '';
        $editedRunSetup = new RunCreateElementCommand();
        $editedRunSetup->setOutput($this->run->getOutput());
        $editedRunSetup->setInput($this->run->getInput());
        $editedRunSetup->setElementObject(new ElementObject());
        $editedRunSetup->setQuestionHelper($this->run->getQuestionHelper());
        $editedRunSetup->setValidators($this->run->getValidators());
        $editedRunSetup->setQuestions(new QuestionsRun($editedRunSetup));
        $editedRunSetup->setFieldTypes(
            GeneralUtility::makeInstance(Typo3FieldTypesConfig::class)->getTCAFieldTypes($table)[$table]
        );
        $editedRunSetup->setTable($table);
        $newInlineFields = new FieldsSetup($editedRunSetup);
        $newInlineFields->createField($table);
        $inlineFields = AdvanceFieldsSetup::getAdvanceFields() + [$inlineKeysOfInlineFields => $newInlineFields->getFields()];

        AdvanceFieldsSetup::setAdvanceFields(
            $inlineFields
        );
    }
}
