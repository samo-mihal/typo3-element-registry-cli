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
     * @var RenderCreateCommand
     */
    protected $render = null;

    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getRegisterIconsClass(): string
    {
        return '\\' . $this->render->getIconRegisterClass() . '::registerIcons(';
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
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function createNewRegistrationIconsFunction($iconPath)
    {
        return
            '
        /**
         * Icon registration
         */
        ' . $this->getRegisterIconsClass() . '
            [
                "' . $iconPath . '",
            ],
            $extKey
        );';
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
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

        GeneralCreateCommandUtility::importStringInToFileAfterString(
            'public/typo3conf/ext/' . $extensionName . '/ext_localconf.php',
            [
                "                '" . $this->render->getElementType() . "/" . str_replace('_', '', $extensionName) . "_" . strtolower($staticName) . "_" . strtolower($name) . "', \n"
            ],
            $this->getRegisterIconsClass(),
            1,
            [
                'newLines' => $this->createNewRegistrationIconsFunction(
                    $this->render->getElementType() . '/' . str_replace('_', '', $extensionName) . '_' . strtolower($staticName) . '_' . strtolower($name)
                    ),
                'universalStringInFile' => 'function ($extKey) {',
                'linesAfterSpecificString' => 0
            ]
        );
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

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function copyPluginDefaultIcon()
    {
        $extensionName = $this->render->getExtensionName();
        $pluginName = $this->render->getName();
        copy(
            "public/typo3conf/ext/content_element_registry/Resources/Public/Icons/CEDefaultIcon.svg",
            "public/typo3conf/ext/" . $extensionName . "/Resources/Public/Icons/" . $pluginName . ".svg"
        );

        GeneralCreateCommandUtility::importStringInToFileAfterString(
            'public/typo3conf/ext/' . $extensionName . '/ext_localconf.php',
            [
                "                '" . $pluginName . "',\n"
            ],
            $this->getRegisterIconsClass(),
            1,
            [
                'newLines' => $this->createNewRegistrationIconsFunction($pluginName),
                'universalStringInFile' => 'function ($extKey) {',
                'linesAfterSpecificString' => 0
            ]
        );
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function copyRecordDefaultIcon()
    {
        $extensionName = $this->render->getExtensionName();
        $recordName = $this->render->getName();
        copy(
            "public/typo3conf/ext/content_element_registry/Resources/Public/Icons/CEDefaultIcon.svg",
            "public/typo3conf/ext/" . $extensionName . "/Resources/Public/Icons/" . $recordName . ".svg"
        );

        GeneralCreateCommandUtility::importStringInToFileAfterString(
            'public/typo3conf/ext/' . $extensionName . '/ext_localconf.php',
            [
                "                '" . $recordName . "',\n"
            ],
            $this->getRegisterIconsClass(),
            1,
            [
                'newLines' => $this->createNewRegistrationIconsFunction($recordName),
                'universalStringInFile' => 'function ($extKey) {',
                'linesAfterSpecificString' => 0
            ]
        );
    }
}
