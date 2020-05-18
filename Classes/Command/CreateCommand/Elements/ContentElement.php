<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;

/**
 * Class ContentElement
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements
 */
class ContentElement extends AbstractElement
{
    /**
     * Table for content elements
     */
    const TABLE = 'tt_content';

    /**
     * ContentElement constructor.
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
    public function createElement()
    {
        $table = $this->elementObject->getTable();
        $name = $this->elementObject->getName();
        $extensionName = $this->elementObject->getExtensionName();

        $this->elementRender->setElement($this->elementObject);
        $this->elementRender->check()->contentElementCreateCommand();
        $this->elementRender->contentElementClass()->template();
        $this->elementRender->model()->contentElementTemplate();
        $this->elementRender->template()->defaultTemplate();
        $this->elementRender->tca()->contentElementTemplate();
        $this->elementRender->icon()->copyContentElementDefaultIcon();
        $this->elementRender->previewImage()->copyDefault();
        $this->elementRender->sqlDatabase()->defaultFields();
        $this->elementRender->flexForm()->contentElementTemplate();
        $this->elementRender->translation()->addStringToTranslation(
            $table . '.' . str_replace('_', '', $extensionName) . '_'. strtolower($name) . '.title',
            $this->elementObject->getTitle()
        );
        $this->elementRender->translation()->addStringToTranslation(
            $table .'.' . str_replace('_', '', $extensionName) . '_'. strtolower($name) . '.description',
            $this->elementObject->getDescription()
        );
        $this->elementRender->translation()->addFieldsTitleToTranslation();
        $this->elementRender->inline()->render();

        $this->elementRender->typo3Cms()->compareDatabase();
        $this->elementRender->typo3Cms()->clearCache();
        $this->elementObject->getOutput()
            ->writeln('<bg=green;options=bold>Content element ' . $name . ' was created.</>');
    }
}
