<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\Typo3FieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\RunCreateElementCommand;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\FieldsSetup;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class InlineSetup
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields
 */
class InlineSetup
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
    protected $inlineItem = '';

    /**
     * @return string
     */
    public function getInlineItem(): string
    {
        return $this->inlineItem;
    }

    /**
     * @param string $inlineItem
     */
    public function setInlineItem(string $inlineItem): void
    {
        $this->inlineItem = $inlineItem;
    }

    /**
     * @return string
     */
    public function createInlineItem()
    {
        $inlineName = $this->run->askInlineClassName();

        $inlineKeysOfInlineFields = RunCreateElementCommand::getArrayKeyOfInlineFields();
        RunCreateElementCommand::setArrayKeyOfInlineFields(RunCreateElementCommand::getArrayKeyOfInlineFields() + 1);

        $inlineTitle = $this->run->askInlineTitle();

        $this->setInlineItem($inlineName . ';' . $inlineKeysOfInlineFields . ';' . $inlineTitle . '*');
        $this->run->getOutput()->writeln(RunCreateElementCommand::getColoredDeepLevel() . 'Create at least one inline field.');

        $table = $this->run->getInlineTable();
        $editedRunSetup = $this->run;
        $editedRunSetup->setFieldTypes(
            GeneralUtility::makeInstance(Typo3FieldTypesConfig::class)->getTCAFieldTypes($table)[$table]
        );
        $newInlineFields = new FieldsSetup($editedRunSetup);
        $newInlineFields->createField();
        $inlineFields = RunCreateElementCommand::getInlineFields() + [$inlineKeysOfInlineFields => $newInlineFields->getFields()];

        RunCreateElementCommand::setInlineFields(
            $inlineFields
        );
    }
}
