<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements\ContentElement;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements\AbstractElement;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;

/**
 * Class Fields
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements\ContentElement
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
        $this->elementRender->check()->contentElementCreateCommand();
        $this->elementRender->contentElementClass()->template();
        $this->elementRender->model()->contentElementTemplate();
        $this->elementRender->tca()->contentElementTemplate();
        $this->elementRender->sqlDatabase()->defaultFields();
        $this->elementRender->flexForm()->contentElementTemplate();
        $this->elementRender->translation()->addFieldsTitleToTranslation();
        $this->elementRender->inline()->render();

        $this->elementRender->typo3Cms()->compareDatabase();
        $this->elementRender->typo3Cms()->clearCache();
        $this->elementObject->getOutput()
            ->writeln('<bg=green;options=bold>Content element ' . $this->elementObject->getName() . ' was modified.</>');
    }
}
