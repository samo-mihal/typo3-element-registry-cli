<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element\Fields;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\Typo3FieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element\FieldsSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\Field\ItemObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\ElementSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\QuestionsSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element\AdvanceFieldsSetup;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class InlineSetup
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element\Fields
 */
class InlineSetup
{
    /**
     * @var string
     */
    protected $inlineTable = 'tx_contentelementregistry_domain_model_relation';

    /**
     * @var ElementSetup
     */
    protected $elementSetup = null;

    /**
     * FieldsSetup constructor.
     * @param ElementSetup $elementSetup
     */
    public function __construct(ElementSetup $elementSetup)
    {
        $this->elementSetup = $elementSetup;
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
     * @return void
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function createInlineItems(FieldObject $field)
    {
        $inlineKeysOfInlineFields = AdvanceFieldsSetup::getArrayKeyOfAdvanceFields();
        AdvanceFieldsSetup::setArrayKeyOfAdvanceFields(AdvanceFieldsSetup::getArrayKeyOfAdvanceFields() + 1);

        $item = new ItemObject();
        $item->setName($this->elementSetup->getQuestions()->askInlineClassName());
        $item->setValue($inlineKeysOfInlineFields);
        $item->setTitle($this->elementSetup->getQuestions()->askInlineTitle());

        $items = $this->getInlineItems();
        $items->attach($item);
        $this->setInlineItems($items);
        if ($field->isDefault() === false) {
            $item->setAdditionalInformation([
                'foreign_field' => $this->elementSetup->getQuestions()->askInlineForeignField()
            ]);
        }

        $this->elementSetup->getOutput()->writeln(QuestionsSetup::getColoredDeepLevel() . 'Create at least one inline field.');

        $table = $field->isDefault() ? $this->getInlineTable() : '';
        $newElementSetup = new ElementSetup($this->elementSetup->getInput(), $this->elementSetup->getOutput());
        $newElementSetup->setQuestionHelper($this->elementSetup->getQuestionHelper());
        $newElementSetup->setFieldTypes(
            GeneralUtility::makeInstance(Typo3FieldTypesConfig::class)->getTCAFieldTypes($table)[$table]
        );
        $newElementSetup->getElementObject()->setTable($table);

        $newInlineFields = new FieldsSetup($newElementSetup);
        $newInlineFields->createField($table);
        $inlineFields = AdvanceFieldsSetup::getAdvanceFields() + [$inlineKeysOfInlineFields => $newInlineFields->getFields()];

        AdvanceFieldsSetup::setAdvanceFields(
            $inlineFields
        );
    }
}
