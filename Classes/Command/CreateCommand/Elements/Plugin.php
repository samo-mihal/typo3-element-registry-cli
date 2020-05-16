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
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param ElementObject $elementObject
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function execute(ElementObject $elementObject)
    {
        $name = $elementObject->getName();
        $elementObject->setStaticName($name);

        $this->elementRender->setElement($elementObject);
        $this->elementRender->check()->pluginCreateCommand();
        $this->elementRender->flexForm()->pluginTemplate();
        $this->elementRender->controller()->template();
        $this->elementRender->template()->pluginTemplate();
        $this->elementRender->typoScript()->addPluginToWizard();
        $this->elementRender->register()->plugin();
        $this->elementRender->register()->pluginFlexForm();
        $this->elementRender->icon()->copyPluginDefaultIcon();
        $this->elementRender->previewImage()->copyPluginDefault();
        $this->elementRender->translation()->addStringToTranslation(
            $elementObject->getTranslationPath(),
            "plugin." . strtolower($name) . ".title",
            $elementObject->getTitle()
        );
        $this->elementRender->translation()->addStringToTranslation(
            $elementObject->getTranslationPath(),
            "plugin." . strtolower($name) . ".description",
            $elementObject->getDescription()
        );

        $this->elementRender->typo3Cms()->compareDatabase();
        $this->elementRender->typo3Cms()->fixFileStructure();
        $this->elementRender->typo3Cms()->clearCache();
        $elementObject->getOutput()->writeln('<bg=green;options=bold>Plugin ' . $name . ' was created.</>');
    }
}
