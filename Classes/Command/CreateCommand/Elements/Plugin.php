<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;

/**
 * Class Plugin
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements
 */
class Plugin extends AbstractElement
{
    /**
     * Plugin constructor.
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
        $name = $this->elementObject->getName();

        $this->elementRender->setElement($this->elementObject);
        $this->elementRender->check()->pluginCreateCommand();
        $this->elementRender->flexForm()->pluginTemplate();
        $this->elementRender->controller()->template();
        $this->elementRender->template()->pluginTemplate();
        $this->elementRender->typoScript()->addPluginToWizard();
        $this->elementRender->register()->plugin();
        $this->elementRender->register()->pluginFlexForm();
        $this->elementRender->icon()->copyElementDefaultIcon();
        $this->elementRender->previewImage()->copyDefault();
        $this->elementRender->translation()->addStringToTranslation(
            "plugin." . strtolower($name) . ".title",
            $this->elementObject->getTitle()
        );
        $this->elementRender->translation()->addStringToTranslation(
            "plugin." . strtolower($name) . ".description",
            $this->elementObject->getDescription()
        );

        $this->elementRender->typo3Cms()->compareDatabase();
        $this->elementRender->typo3Cms()->clearCache();
        $this->elementObject->getOutput()->writeln('<bg=green;options=bold>Plugin ' . $name . ' was created.</>');
    }
}
