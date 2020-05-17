<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element\Fields;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\Field\ItemObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\ElementSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\QuestionsSetup;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class ItemsSetup
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element\Fields
 */
class ItemsSetup
{
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
        $this->items = GeneralUtility::makeInstance(ObjectStorage::class);
    }

    /**
     * @var ObjectStorage<ItemObject>
     */
    protected $items = null;

    /**
     * @return ObjectStorage
     */
    public function getItems(): ObjectStorage
    {
        return $this->items;
    }

    /**
     * @param ObjectStorage $items
     */
    public function setItems(ObjectStorage $items): void
    {
        $this->items = $items;
    }


    public function createItem()
    {
        $item = new ItemObject();
        $item->setName($this->elementSetup->getQuestions()->askItemName());
        $item->setValue($this->elementSetup->getQuestions()->askItemValue());
        $item->setTitle($this->elementSetup->getQuestions()->askItemTitle());

        $items = $this->getItems();
        $items->attach($item);
        $this->setItems($items);

        if ($this->elementSetup->getQuestions()->needCreateMoreItems()) {
            $this->createItem();
        } else {
            QuestionsSetup::setDeepLevelUp();
        }
    }
}
