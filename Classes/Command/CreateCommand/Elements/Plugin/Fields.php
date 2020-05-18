<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements\Plugin;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements\AbstractElement;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;

/**
 * Class Fields
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements\Plugin
 */
class Fields extends AbstractElement
{
    /**
     * Fields constructor.
     * @param ElementObject $elementObject
     */
    public function __construct(ElementObject $elementObject)
    {
        parent::__construct($elementObject);
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function addFields()
    {
        $this->elementRender->setElement($this->elementObject);
        $this->elementRender->check()->pluginCreateCommand();
        $this->elementRender->register()->pluginFlexForm();
        $this->elementRender->flexForm()->pluginTemplate();

        $this->elementRender->typo3Cms()->compareDatabase();
        $this->elementRender->typo3Cms()->clearCache();
        $this->elementObject->getOutput()->writeln(
            '<bg=green;options=bold>Plugin ' . $this->elementObject->getName() . ' was created.</>'
        );
    }
}
