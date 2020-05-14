<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Utility\FieldsCreateCommandUtility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Plugin
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand
 */
class PluginCreateCommand extends Command
{

    protected function configure()
    {
        $this->addArgument('vendor', InputArgument::REQUIRED,'Enter vendor of plugin namespace');
        $this->addArgument('main-extension', InputArgument::REQUIRED,'Enter main extension of plugin');
        $this->addArgument('extension', InputArgument::REQUIRED,'Enter extension of plugin');
        $this->addArgument('name', InputArgument::REQUIRED,'Enter name of Plugin.');
        $this->addArgument('title', InputArgument::REQUIRED,'Enter title of Plugin.');
        $this->addArgument('description', InputArgument::REQUIRED,'Enter description of Plugin.');
        $this->addArgument('controller', InputArgument::REQUIRED,'Enter controller name of Plugin.');
        $this->addArgument('action', InputArgument::REQUIRED,'Enter action name of Plugin in controller.');
        $this->addArgument('fields', InputArgument::REQUIRED,'Enter fields of fields.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pluginName = $input->getArgument('name');
        $pluginTitle = $input->getArgument('title');
        $pluginDescription = $input->getArgument('description');
        $controllerName = $input->getArgument('controller');
        $actionName = $input->getArgument('action');
        $fields = $input->getArgument('fields');
        $mainExtension = $input->getArgument('main-extension');
        $vendor = $input->getArgument('vendor');
        $extensionName = $input->getArgument('extension');

        $fields = GeneralUtility::makeInstance(FieldsCreateCommandUtility::class)->generateObject($fields, '');

        $element = GeneralUtility::makeInstance(ElementRender::class);
        $element->setExtensionName($extensionName);
        $element->setElement($fields);
        $element->setName($pluginName);
        $element->setStaticName($pluginName);
        $element->setType('Plugin');
        $element->setOutput($output);
        $element->setInput($input);
        $element->setControllerName($controllerName);
        $element->setActionName($actionName);
        $element->setTitle($pluginTitle);
        $element->setVendor($vendor);
        $element->setMainExtension($mainExtension);

        $element->check()->pluginCreateCommand();
        $element->flexForm()->pluginTemplate();
        $element->controller()->template();
        $element->template()->pluginTemplate();
        $element->typoScript()->addPluginToWizard();
        $element->register()->plugin();
        $element->register()->pluginFlexForm();
        $element->icon()->copyPluginDefaultIcon();
        $element->previewImage()->copyPluginDefault();
        $element->translation()->addStringToTranslation(
            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf',
            "plugin." . strtolower($pluginName) . ".title",
            $pluginTitle
        );
        $element->translation()->addStringToTranslation(
            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf',
            "plugin." . strtolower($pluginName) . ".description",
            $pluginDescription
        );

        $output->writeln('<bg=red;options=bold>• Fill template: public/typo3conf/ext/' . $extensionName . '/Resources/Private/Templates/' . $controllerName . '/' . ucfirst($actionName) . '.html</>');
        $output->writeln('<bg=red;options=bold>• Change Plugin Icon.</>');
        $output->writeln('<bg=red;options=bold>• Change Plugin Preview image.</>');
        $element->typo3Cms()->compareDatabase();
        $element->typo3Cms()->fixFileStructure();
        $element->typo3Cms()->clearCache();
        $output->writeln('<bg=green;options=bold>Plugin ' . $pluginName . ' was created.</>');
    }
}
