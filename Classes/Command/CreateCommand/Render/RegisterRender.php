<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;

/**
 * Class Register
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render
 */
class RegisterRender
{
    /**
     * @var RenderCreateCommand
     */
    protected $render = null;

    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
    }

    public function pageTypeToExtTables()
    {
        $pageTypeName = $this->render->getName();
        $extensionName = $this->render->getExtensionName();

        GeneralCreateCommandUtility::importStringInToFileAfterString(
            'public/typo3conf/ext/' . $extensionName . '/ext_tables.php',
            [
                "        Digitalwerk\Typo3ElementRegistryCli\Utility\Typo3ElementRegistryCliUtility::addPageDoktype(" . $pageTypeName . "::getDoktype()); \n"
            ],
            'call_user_func(',
            1
        );

        GeneralCreateCommandUtility::importStringInToFileAfterString(
            'public/typo3conf/ext/' . $extensionName . '/ext_tables.php',
            [
                "\nuse " . $this->render->getModelNamespace() . "\\" . $pageTypeName . ";"
            ],
            '',
            -1

        );
    }

    public function pluginFlexForm()
    {
        if ($this->render->getFields()) {
            $pluginName = $this->render->getName();
            $extensionName = $this->render->getExtensionName();
            $pluginIconEdited = 'EXT:' . $extensionName . '/Resources/Public/Icons/' . $pluginName . '.svg';
            GeneralCreateCommandUtility::importStringInToFileAfterString(
                'public/typo3conf/ext/' . $extensionName . '/Configuration/TCA/Overrides/tt_content.php',
                [
                    "\nBoilerplateUtility::addPluginFlexForm('" . $extensionName . "', '" . $pluginName . "');\n"
                ],
                "'" . $pluginIconEdited . "'",
                1

            );
        }
    }

    public function plugin()
    {
        $pluginName = $this->render->getName();
        $extensionName = $this->render->getExtensionName();
        $pluginIconEdited = 'EXT:' . $extensionName . '/Resources/Public/Icons/' . $pluginName . '.svg';
        $pluginTitle = $this->render->getTitle();
        $controllerName = $this->render->getControllerName();
        $actionName = $this->render->getActionName();

        GeneralCreateCommandUtility::importStringInToFileAfterString(
            'public/typo3conf/ext/' . $extensionName . '/Configuration/TCA/Overrides/tt_content.php',
            [
                "
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
'Digitalwerk." . str_replace(' ','',ucwords(str_replace('_',' ',$extensionName))) . "',
'" . $pluginName . "',
'" . str_replace('-',' ',$pluginTitle) . "',
'" . $pluginIconEdited . "'
);
"
            ],
            'defined(\'TYPO3_MODE\') or die();',
            0
        );

        GeneralCreateCommandUtility::importStringInToFileAfterString(
            'public/typo3conf/ext/' . $extensionName . '/ext_localconf.php',
            [
                "
        /**
         * " . str_replace('-',' ',$pluginTitle) . "
        */
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
         'Digitalwerk." . str_replace(' ','',ucwords(str_replace('_',' ',$extensionName))) . "',
          '" . $pluginName . "',
          ['" . $controllerName . "' => '". strtolower($actionName) . "'],
          ['" . $controllerName . "' => '']
        );
"
            ],
            'call_user_func(',
            1

        );
    }
}
