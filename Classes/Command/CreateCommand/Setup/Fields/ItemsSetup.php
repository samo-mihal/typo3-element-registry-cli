<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\Field\ItemObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Run\QuestionsRun;
use Digitalwerk\Typo3ElementRegistryCli\Command\RunCreateElementCommand;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class ItemsSetup
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element
 */
class ItemsSetup
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
        $item->setName($this->run->getQuestions()->askItemName());
        $item->setValue($this->run->getQuestions()->askItemValue());
        $item->setTitle($this->run->getQuestions()->askItemTitle());

        $items = $this->getItems();
        $items->attach($item);
        $this->setItems($items);

        if ($this->run->getQuestions()->needCreateMoreItems()) {
            $this->createItem();
        } else {
            QuestionsRun::setDeepLevelUp();
        }
    }
}
