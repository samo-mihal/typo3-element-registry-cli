<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element\Fields;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\FlexFormFieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\Field\ItemObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\ElementSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\QuestionsSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element\AdvanceFieldsSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element\Fields\FlexForm\FlexFormFieldsSetup;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class FlexForm
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element
 */
class FlexFormSetup
{
    /**
     * @var ElementSetup
     */
    protected $elementSetup = null;

    /**
     * @var ObjectStorage
     */
    protected $flexFormItems = null;

    /**
     * FieldsSetup constructor.
     * @param ElementSetup $elementSetup
     */
    public function __construct(ElementSetup $elementSetup)
    {
        $this->elementSetup = $elementSetup;
        $this->flexFormItems = GeneralUtility::makeInstance(ObjectStorage::class);
    }

    /**
     * @return ObjectStorage
     */
    public function getFlexFormItems(): ObjectStorage
    {
        return $this->flexFormItems;
    }

    /**
     * @param ObjectStorage $flexFormItems
     */
    public function setFlexFormItems(ObjectStorage $flexFormItems): void
    {
        $this->flexFormItems = $flexFormItems;
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function createFlexForm()
    {
        $inlineKeysOfInlineFields = AdvanceFieldsSetup::getArrayKeyOfAdvanceFields();
        AdvanceFieldsSetup::setArrayKeyOfAdvanceFields(AdvanceFieldsSetup::getArrayKeyOfAdvanceFields() + 1);

        $item = new ItemObject();
        $item->setName('NoDefined');
        $item->setValue($inlineKeysOfInlineFields);
        $item->setTitle('NoDefined');

        $items = $this->getFlexFormItems();
        $items->attach($item);
        $this->setFlexFormItems($items);

        $this->elementSetup->getOutput()->writeln(QuestionsSetup::getColoredDeepLevel() . 'Create at least one flexForm field.');

        $editedRunSetup = $this->elementSetup;
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
