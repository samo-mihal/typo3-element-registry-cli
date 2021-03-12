<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command;

use Digitalwerk\Typo3ElementRegistryCli\ElementObjects\PluginObject;
use Digitalwerk\Typo3ElementRegistryCli\Utility\ControllerUtility;
use Digitalwerk\Typo3ElementRegistryCli\Utility\FileUtility;
use Digitalwerk\Typo3ElementRegistryCli\Utility\PluginUtility;
use Symfony\Component\Console\Question\ChoiceQuestion;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use function Symfony\Component\String\u;

/**
 * Class PluginMakeCommand
 * @package Digitalwerk\Typo3ElementRegistryCli\Command
 */
class PluginMakeCommand extends AbstractMakeCommand
{
    /**
     * Default constants
     */
    const DEFAULT_REGISTER_PLUGIN_TEMPLATE =
        'EXT:typo3_element_registry_cli/Resources/Private/Templates/Plugin/RegisterPlugin.txt';
    const DEFAULT_CONFIG_PLUGIN_TEMPLATE =
        'EXT:typo3_element_registry_cli/Resources/Private/Templates/Plugin/ConfigPlugin.txt';
    const DEFAULT_TEMPLATE_TEMPLATE =
        'EXT:typo3_element_registry_cli/Resources/Private/Templates/Plugin/Template.txt';
    const DEFAULT_CONTROLLER_TEMPLATE =
        'EXT:typo3_element_registry_cli/Resources/Private/Templates/Plugin/Controller.txt';
    const DEFAULT_CONTROLLER_EXTEND =
        'TYPO3\CMS\Extbase\Mvc\Controller\ActionController';

    /**
     * @var array
     */
    protected $requiredFiles = [
        'EXT:{extension}/Configuration/TCA/Overrides/tt_content.php',
        'EXT:{extension}/ext_localconf.php'
    ];

    /**
     * @var bool
     */
    protected $hasTable = false;

    /**
     * @var string
     */
    protected $controllerExtend = self::DEFAULT_CONTROLLER_EXTEND;

    /**
     * @var PluginObject
     */
    protected $pluginObject = null;

    /**
     * @return void
     */
    public function beforeMake(): void
    {
        $this->extension = $this->questionHelper->ask(
            $this->input,
            $this->output,
            (new ChoiceQuestion(
                'Plugin extension: ',
                array_keys($GLOBALS['TYPO3_LOADED_EXT'])
            )
            )
        );
        $this->controllerExtend = $this->typo3ElementRegistryCliConfig['plugin']['controllerExtend'];

        $this->pluginObject = (new PluginObject($this->input, $this->output, $this->questionHelper, $this));
        $this->pluginObject->questions();

        parent::beforeMake();
    }

    /**
     * @return void
     */
    public function make(): void
    {
        /** Register plugin */
        $registerPlugin = file_get_contents(
            GeneralUtility::getFileAbsFileName(self::DEFAULT_REGISTER_PLUGIN_TEMPLATE)
        );
        $registerPlugin = str_replace([
            '{vendor}', '{extensionCamelCase}', '{name}', '{title}'
        ], [
            $this->vendor,
            u($this->extension)->camel()->title(true),
            $this->pluginObject->getName(),
            $this->pluginObject->getTitle()
        ], $registerPlugin);
        PluginUtility::registerPlugin(
            'EXT:' . $this->extension . '/Configuration/TCA/Overrides/tt_content.php',
            $registerPlugin,
            $this->output
        );

        /** Register plugin */
        $configPlugin = file_get_contents(
            GeneralUtility::getFileAbsFileName(self::DEFAULT_CONFIG_PLUGIN_TEMPLATE)
        );
        $configPlugin = str_replace([
            '{vendor}', '{extensionCamelCase}', '{name}', '{title}', '{controller}', '{action}'
        ], [
            $this->vendor,
            u($this->extension)->camel()->title(true),
            $this->pluginObject->getName(),
            $this->pluginObject->getTitle(),
            $this->pluginObject->getControllerName(),
            $this->pluginObject->getActionName()
        ], $configPlugin);
        PluginUtility::configPlugin(
            'EXT:' . $this->extension . '/ext_localconf.php',
            $configPlugin,
            $this->output
        );

        /** Generate template */
        FileUtility::createFile(
            'EXT:' . $this->extension . '/Resources/Private/Templates/' .
            $this->pluginObject->getName() . '/' . ucfirst($this->pluginObject->getActionName()) . '.html',
            file_get_contents(
                GeneralUtility::getFileAbsFileName(self::DEFAULT_TEMPLATE_TEMPLATE)
            )
        );

        /** Generate controller action */
        $controllerPath = GeneralUtility::getFileAbsFileName(
            'EXT:' . $this->extension . '/Classes/Controller/' .
            $this->pluginObject->getControllerName() . 'Controller.php'
        );

        if (file_exists($controllerPath) === false) {
            $controllerTemplate = file_get_contents(
                GeneralUtility::getFileAbsFileName(self::DEFAULT_CONTROLLER_TEMPLATE)
            );
            $controllerTemplate = str_replace([
                '{vendor}', '{extensionCamelCase}', '{controllerName}', '{extend}'
            ], [
                $this->vendor,
                u($this->extension)->camel()->title(true),
                $this->pluginObject->getControllerName(),
                $this->controllerExtend,
            ], $controllerTemplate);
            FileUtility::createFile($controllerPath, $controllerTemplate);
        }

        ControllerUtility::createAction($controllerPath, $this->pluginObject->getActionName());
    }
}
