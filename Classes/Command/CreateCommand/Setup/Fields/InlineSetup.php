<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\Typo3FieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Run\QuestionsRun;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\AdvanceFieldsSetup;
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
     * @return string
     */
    public function getInlineTable(): string
    {
        return $this->inlineTable;
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
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function createInlineItem()
    {
        $inlineName = $this->run->getQuestions()->askInlineClassName();

        $inlineKeysOfInlineFields = AdvanceFieldsSetup::getArrayKeyOfAdvanceFields();
        AdvanceFieldsSetup::setArrayKeyOfAdvanceFields(AdvanceFieldsSetup::getArrayKeyOfAdvanceFields() + 1);

        $inlineTitle = $this->run->getQuestions()->askInlineTitle();

        $this->setInlineItem($inlineName . ';' . $inlineKeysOfInlineFields . ';' . $inlineTitle . '*');
        $this->run->getOutput()->writeln(QuestionsRun::getColoredDeepLevel() . 'Create at least one inline field.');

        $table = $this->getInlineTable();
        $editedRunSetup = $this->run;
        $editedRunSetup->setFieldTypes(
            GeneralUtility::makeInstance(Typo3FieldTypesConfig::class)->getTCAFieldTypes($table)[$table]
        );
        $newInlineFields = new FieldsSetup($editedRunSetup);
        $newInlineFields->createField();
        $inlineFields = AdvanceFieldsSetup::getAdvanceFields() + [$inlineKeysOfInlineFields => $newInlineFields->getFields()];

        AdvanceFieldsSetup::setAdvanceFields(
            $inlineFields
        );
    }
}
