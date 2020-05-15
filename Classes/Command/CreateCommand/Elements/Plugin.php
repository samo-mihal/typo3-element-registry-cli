<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Plugin
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements
 */
class Plugin
{
    /**
     * @param ElementObject $elementObject
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function execute(ElementObject $elementObject)
    {
        $name = $elementObject->getName();
        $elementObject->setStaticName($name);

        $elementRender = GeneralUtility::makeInstance(ElementRender::class);
        $elementRender->setElement($elementObject);
        $elementRender->check()->pluginCreateCommand();
        $elementRender->flexForm()->pluginTemplate();
        $elementRender->controller()->template();
        $elementRender->template()->pluginTemplate();
        $elementRender->typoScript()->addPluginToWizard();
        $elementRender->register()->plugin();
        $elementRender->register()->pluginFlexForm();
        $elementRender->icon()->copyPluginDefaultIcon();
        $elementRender->previewImage()->copyPluginDefault();
        $elementRender->translation()->addStringToTranslation(
            $elementObject->getTranslationPath(),
            "plugin." . strtolower($name) . ".title",
            $elementObject->getTitle()
        );
        $elementRender->translation()->addStringToTranslation(
            $elementObject->getTranslationPath(),
            "plugin." . strtolower($name) . ".description",
            $elementObject->getDescription()
        );

        $elementRender->typo3Cms()->compareDatabase();
        $elementRender->typo3Cms()->fixFileStructure();
        $elementRender->typo3Cms()->clearCache();
        $elementObject->getOutput()->writeln('<bg=green;options=bold>Plugin ' . $name . ' was created.</>');
    }
}
