<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RunCreateCommand;

/**
 * Class ItemsSetup
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields
 */
class ItemsSetup
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
        $itemName = $this->run->askItemName();
        $itemValue = $this->run->askItemValue();
        $itemTitle = $this->run->askItemTitle();

        $item = $itemName . ';' . $itemValue . ';' . $itemTitle . '*';

        $this->setItems($this->getItems() . $item);

        if ($this->run->needCreateMoreItems()) {
            $this->createItem();
        } else {
            RunCreateCommand::setDeepLevel(substr(RunCreateCommand::getRawDeepLevel(), 0, -strlen(RunCreateCommand::DEEP_LEVEL_SPACES)));
        }
    }
}
