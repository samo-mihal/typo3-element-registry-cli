<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements\ContentElement;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements\PageType;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements\Plugin;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements\Record;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element\AdvanceFieldsSetup;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ElementSetup
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup
 */
class ElementSetup extends AbstractSetup
{
    /**
     * Element type constants
     */
    const CONTENT_ELEMENT = 'Content element';
    const PAGE_TYPE = 'Page Type';
    const PLUGIN = 'Plugin';
    const RECORD = 'Record';

    /**
     * ElementSetup constructor.
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        parent::__construct($input, $output);
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function initialize()
    {
        $this->validators->validateContentElementRegistrySettings();
        $this->validators->validateTypo3ElementRegistryCliSettings();
        $this->validators->validateCreateCommandConfigDataStructure();
        $this->elementObject->setMainExtension($this->getMainExtension());

        $this->elementObject->setType(
            $this->questions->askElementType()
        );

        if ($this->elementObject->getType() === self::CONTENT_ELEMENT){
            $this->elementObject->setTable(ContentElement::TABLE);
            $this->elementObject->setExtensionName(
                $this->questions->askExtensionName()
            );
            $this->elementObject->setName(
                $this->questions->askElementName()
            );
            $this->elementObject->setTitle(
                $this->questions->askElementTitle()
            );
            $this->elementObject->setDescription(
                $this->questions->askElementDescription()
            );
            $this->elementObject->setFields(
                $this->questions->askTCAFields()
            );
            $this->elementObject->setInlineFields(
                AdvanceFieldsSetup::getAdvanceFields()
            );
            GeneralUtility::makeInstance(ContentElement::class)->execute($this->elementObject);
        } elseif ($this->elementObject->getType() === self::PAGE_TYPE) {
            $this->elementObject->setTable(PageType::TABLE);
            $this->elementObject->setExtensionName(
                $this->questions->askExtensionName()
            );
            $this->elementObject->setName(
                $this->questions->askElementName()
            );
            $this->elementObject->setTitle(
                $this->questions->askElementTitle()
            );
            $this->elementObject->setDoktype(
                $this->questions->askPageTypeDoktype()
            );
            $this->elementObject->setAutoHeader(
                $this->questions->needPageTypeAutoHeader()
            );
            $this->elementObject->setFields(
                $this->questions->askTCAFields()
            );
            $this->elementObject->setInlineFields(
                AdvanceFieldsSetup::getAdvanceFields()
            );
            GeneralUtility::makeInstance(PageType::class)->execute($this->elementObject);
        } elseif ($this->elementObject->getType() === self::PLUGIN) {
            $this->elementObject->setExtensionName(
                $this->questions->askExtensionName()
            );
            $this->elementObject->setName(
                $this->questions->askElementName()
            );
            $this->elementObject->setTitle(
                $this->questions ->askElementTitle()
            );
            $this->elementObject->setDescription(
                $this->questions->askElementDescription()
            );
            $this->elementObject->setControllerName(
                $this->questions->askPluginController()
            );
            $this->elementObject->setActionName(
                $this->questions->askPluginAction()
            );
            $this->questions->askFlexFormFields();
            GeneralUtility::makeInstance(Plugin::class)->execute($this->elementObject);
        } elseif ($this->elementObject->getType() === self::RECORD) {
            $this->elementObject->setExtensionName(
                $this->questions->askExtensionName()
            );
            $this->elementObject->setName(
                $this->questions->askElementName()
            );
            $this->elementObject->setTitle(
                $this->questions->askElementTitle()
            );
            $this->elementObject->setFields(
                $this->questions->askTCAFields()
            );
            $this->elementObject->setInlineFields(
                AdvanceFieldsSetup::getAdvanceFields()
            );
            GeneralUtility::makeInstance(Record::class)->execute($this->elementObject);
        }
    }
}
