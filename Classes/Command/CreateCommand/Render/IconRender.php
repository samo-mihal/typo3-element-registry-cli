<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;

/**
 * Class Icon
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render
 */
class IconRender
{
    /**
     * @var string
     */
    protected $registerIconsString = '\Digitalwerk\ContentElementRegistry\Utility\ContentElementRegistryUtility::registerIcons(';

    /**
     * @var RenderCreateCommand
     */
    protected $render = null;

    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
    }

    public function copyContentElementDefaultIcon()
    {
        $extensionName = $this->render->getExtensionName();
        $name = $this->render->getName();
        copy(
            'public/typo3conf/ext/content_element_registry/Resources/Public/Icons/CEDefaultIcon.svg',
            'public/typo3conf/ext/' . $extensionName . '/Resources/Public/Icons/ContentElement/' . str_replace('_', '', $extensionName) . '_' . strtolower($name) . '.svg'
        );
    }

    /**
     * @param $iconPath
     * @return string
     */
    public function createNewRegistrationIconsFunction($iconPath)
    {
        return
            '
        /**
         * Icon registration
         */
        ' . $this->registerIconsString . '
            [
                "' . $iconPath . '",
            ],
            $extKey
        );';
    }

    public function copyAndRegisterInlineDefaultIcon()
    {
        $extensionName = $this->render->getExtensionName();
        $staticName = $this->render->getStaticName();
        $name = $this->render->getName();
        if (!file_exists('public/typo3conf/ext/' . $extensionName . '/Resources/Public/Icons/' . $this->render->getElementType())) {
            mkdir('public/typo3conf/ext/' . $extensionName . '/Resources/Public/Icons/' . $this->render->getElementType(), 0777, true);
        }
        copy(
            'public/typo3conf/ext/content_element_registry/Resources/Public/Icons/CEDefaultIcon.svg',
            'public/typo3conf/ext/' . $extensionName . '/Resources/Public/Icons/' . $this->render->getElementType() . '/' .
            str_replace('_', '', $extensionName) . '_' .
            strtolower($this->render->getStaticName()) . '_'.
            strtolower($this->render->getName()) . '.svg'
        );

        if (
            !GeneralCreateCommandUtility::importStringInToFileAfterString(
                'public/typo3conf/ext/' . $extensionName . '/ext_localconf.php',
                [
                    "                '" . $this->render->getElementType() . "/" . str_replace('_', '', $extensionName) . "_" . strtolower($staticName) . "_" . strtolower($name) . "', \n"
                ],
                $this->registerIconsString,
                1
            )
        ) {
            GeneralCreateCommandUtility::importStringInToFileAfterString(
                'public/typo3conf/ext/' . $extensionName . '/ext_localconf.php',
                [
                    $this->createNewRegistrationIconsFunction(
                        $this->render->getElementType() . '/' . str_replace('_', '', $extensionName) . '_' . strtolower($staticName) . '_' . strtolower($name)
                    )
                ],
                'function ($extKey) {',
                0
            );
        }
    }

    public function copyPageTypeDefaultIcon()
    {
        $extensionName = $this->render->getExtensionName();
        $doktype = $this->render->getDoktype();

        copy(
            'public/typo3conf/ext/content_element_registry/Resources/Public/Icons/CEDefaultIcon.svg',
            'public/typo3conf/ext/' . $extensionName . '/Resources/Public/Icons/dw-page-type-' . $doktype . '.svg'
        );
        copy(
            'public/typo3conf/ext/content_element_registry/Resources/Public/Icons/CEDefaultIcon.svg',
            'public/typo3conf/ext/' . $extensionName . '/Resources/Public/Icons/dw-page-type-' . $doktype . '-not-in-menu.svg'
        );
    }

    public function copyPluginDefaultIcon()
    {
        $extensionName = $this->render->getExtensionName();
        $pluginName = $this->render->getName();
        copy(
            "public/typo3conf/ext/content_element_registry/Resources/Public/Icons/CEDefaultIcon.svg",
            "public/typo3conf/ext/" . $extensionName . "/Resources/Public/Icons/" . $pluginName . ".svg"
        );

        if (
            !GeneralCreateCommandUtility::importStringInToFileAfterString(
            'public/typo3conf/ext/' . $extensionName . '/ext_localconf.php',
            [
                "                '" . $pluginName . "',\n"
            ],
            $this->registerIconsString,
            1
            )
        ) {
            GeneralCreateCommandUtility::importStringInToFileAfterString(
                'public/typo3conf/ext/' . $extensionName . '/ext_localconf.php',
                [
                    $this->createNewRegistrationIconsFunction($pluginName)
                ],
                'function ($extKey) {',
                0
            );
        }
    }

    public function copyRecordDefaultIcon()
    {
        $extensionName = $this->render->getExtensionName();
        $recordName = $this->render->getName();
        copy(
            "public/typo3conf/ext/content_element_registry/Resources/Public/Icons/CEDefaultIcon.svg",
            "public/typo3conf/ext/" . $extensionName . "/Resources/Public/Icons/" . $recordName . ".svg"
        );

        if (
        !GeneralCreateCommandUtility::importStringInToFileAfterString(
            'public/typo3conf/ext/' . $extensionName . '/ext_localconf.php',
            [
                "                '" . $recordName . "',\n"
            ],
            $this->registerIconsString,
            1
        )
        ) {
            GeneralCreateCommandUtility::importStringInToFileAfterString(
                'public/typo3conf/ext/' . $extensionName . '/ext_localconf.php',
                [
                    $this->createNewRegistrationIconsFunction($recordName)
                ],
                'function ($extKey) {',
                0
            );
        }
    }
}
