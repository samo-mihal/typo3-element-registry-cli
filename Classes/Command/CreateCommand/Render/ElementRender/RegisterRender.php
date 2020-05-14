<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;

/**
 * Class RegisterRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
class RegisterRender extends AbstractRender
{
    /**
     * RegisterRender constructor.
     * @param ElementRender $elementRender
     */
    public function __construct(ElementRender $elementRender)
    {
        parent::__construct($elementRender);
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function pageTypeToExtTables()
    {
        $pageTypeName = $this->elementRender->getElement()->getName();
        $extensionName = $this->elementRender->getElement()->getExtensionName();

        GeneralCreateCommandUtility::importStringInToFileAfterString(
            'public/typo3conf/ext/' . $extensionName . '/ext_tables.php',
            [
                "        " . $this->elementRender->getElement()->getRegisterPageDoktypeClass() . "::addPageDoktype(" . $pageTypeName . "::getDoktype()); \n"
            ],
            'call_user_func(',
            1
        );

        GeneralCreateCommandUtility::importStringInToFileAfterString(
            'public/typo3conf/ext/' . $extensionName . '/ext_tables.php',
            [
                "\nuse " . $this->elementRender->getElement()->getModelNamespace() . "\\" . $pageTypeName . ";"
            ],
            '',
            -1
        );
    }

    public function pluginFlexForm()
    {
        if ($this->elementRender->getElement()->getFields()) {
            $pluginName = $this->elementRender->getElement()->getName();
            $extensionName = $this->elementRender->getElement()->getExtensionName();
            $pluginIconEdited = 'EXT:' . $extensionName . '/Resources/Public/Icons/' . $pluginName . '.svg';
            GeneralCreateCommandUtility::importStringInToFileAfterString(
                'public/typo3conf/ext/' . $extensionName . '/Configuration/TCA/Overrides/tt_content.php',
                [
                    "\n\Digitalwerk\Typo3ElementRegistryCli\Utility\Typo3ElementRegistryCliUtility::addPluginFlexForm('" . $extensionName . "', '" . $pluginName . "');\n"
                ],
                "'" . $pluginIconEdited . "'",
                1

            );
        }
    }

    public function plugin()
    {
        $pluginName = $this->elementRender->getElement()->getName();
        $extensionName = $this->elementRender->getElement()->getExtensionName();
        $pluginIconEdited = 'EXT:' . $extensionName . '/Resources/Public/Icons/' . $pluginName . '.svg';
        $pluginTitle = $this->elementRender->getElement()->getTitle();
        $controllerName = $this->elementRender->getElement()->getControllerName();
        $actionName = $this->elementRender->getElement()->getActionName();

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
          ['" . $controllerName . "' => '". $actionName . "'],
          ['" . $controllerName . "' => '']
        );
"
            ],
            'call_user_func(',
            1

        );
    }
}
