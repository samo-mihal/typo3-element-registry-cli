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
    const INLINE = 'Inline';

    /**
     * Action constants
     */
    const CREATE_NEW_ELEMENT = 'Create a new element';
    const EDIT_EXISTING_ELEMENT = 'Edit existing element';
    const ADD_FIELDS_TO_EXISTING_ELEMENT = 'Add fields to existing element';

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

        $action = $this->questions->askElementAction();
        if ($action === self::CREATE_NEW_ELEMENT) {
            $this->createElement();
        } elseif ($action === self::EDIT_EXISTING_ELEMENT) {
            $this->editExistingElement();
        }

    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     * @return void
     */
    private function createElement(): void
    {
        $this->elementObject->setExtensionName(
            $this->questions->askExtensionName()
        );
        $this->elementObject->setName(
            $this->questions->askElementName()
        );

        if ($this->elementObject->getType() === self::CONTENT_ELEMENT){
            $this->elementObject->setTable(ContentElement::TABLE);
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
            GeneralUtility::makeInstance(ContentElement::class, $this->elementObject)->createElement();
        } elseif ($this->elementObject->getType() === self::PAGE_TYPE) {
            $this->elementObject->setTable(PageType::TABLE);
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
            GeneralUtility::makeInstance(PageType::class, $this->elementObject)->createElement();
        } elseif ($this->elementObject->getType() === self::PLUGIN) {
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
            GeneralUtility::makeInstance(Plugin::class, $this->elementObject)->createElement();
        } elseif ($this->elementObject->getType() === self::RECORD) {
            $this->elementObject->setTitle(
                $this->questions->askElementTitle()
            );
            $this->elementObject->setFields(
                $this->questions->askTCAFields()
            );
            $this->elementObject->setInlineFields(
                AdvanceFieldsSetup::getAdvanceFields()
            );
            GeneralUtility::makeInstance(Record::class, $this->elementObject)->createElement();
        }
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     * @return void
     */
    private function editExistingElement(): void
    {
        $editElementAction = $this->questions->askEditElementAction();
        if ($editElementAction === self::ADD_FIELDS_TO_EXISTING_ELEMENT) {
            $this->addFieldsToExistingElement();
        }
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     * @return void
     */
    private function addFieldsToExistingElement(): void
    {
        $this->elementObject->setExtensionName(
            $this->questions->askExtensionName()
        );
        $this->elementObject->setName(
            $this->questions->askElement()
        );

        if ($this->elementObject->getType() === self::CONTENT_ELEMENT) {
            $this->elementObject->setTable(
                ContentElement::TABLE
            );
            $this->elementObject->setFields(
                $this->questions->askTCAFields()
            );
            $this->elementObject->setInlineFields(
                AdvanceFieldsSetup::getAdvanceFields()
            );
            GeneralUtility::makeInstance(ContentElement\Fields::class, $this->elementObject)->addFields();
        } elseif ($this->elementObject->getType() === self::PAGE_TYPE) {
            $this->elementObject->setTable(PageType::TABLE);
            $this->elementObject->setFields(
                $this->questions->askTCAFields()
            );
            $this->elementObject->setInlineFields(
                AdvanceFieldsSetup::getAdvanceFields()
            );
            GeneralUtility::makeInstance(PageType\Fields::class, $this->elementObject)->addFields();
        } elseif ($this->elementObject->getType() === self::RECORD) {
            $this->elementObject->setFields(
                $this->questions->askTCAFields()
            );
            $this->elementObject->setInlineFields(
                AdvanceFieldsSetup::getAdvanceFields()
            );
            GeneralUtility::makeInstance(Record\Fields::class, $this->elementObject)->addFields();
        } elseif ($this->elementObject->getType() === self::PLUGIN) {
            $this->questions->askFlexFormFields();
            GeneralUtility::makeInstance(Plugin\Fields::class, $this->elementObject)->addFields();
        }
    }
}
