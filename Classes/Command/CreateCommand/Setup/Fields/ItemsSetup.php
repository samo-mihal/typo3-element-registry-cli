<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Run\QuestionsRun;
use Digitalwerk\Typo3ElementRegistryCli\Command\RunCreateElementCommand;

/**
 * Class ItemsSetup
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields
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
     * @var string
     */
    protected $items = '';

    /**
     * @return string
     */
    public function getItems(): string
    {
        return $this->items;
    }

    /**
     * @param string $items
     */
    public function setItems(string $items): void
    {
        $this->items = $items;
    }


    public function createItem()
    {
        $itemName = $this->run->getQuestions()->askItemName();
        $itemValue = $this->run->getQuestions()->askItemValue();
        $itemTitle = $this->run->getQuestions()->askItemTitle();

        $item = $itemName . ';' . $itemValue . ';' . $itemTitle . '*';

        $this->setItems($this->getItems() . $item);

        if ($this->run->getQuestions()->needCreateMoreItems()) {
            $this->createItem();
        } else {
            QuestionsRun::setDeepLevelUp();
        }
    }
}
