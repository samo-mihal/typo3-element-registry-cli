<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;

/**
 * Class IconRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
class IconRender extends AbstractRender
{
    /**
     * IconRender constructor.
     * @param ElementRender $element
     */
    public function __construct(ElementRender $element)
    {
        parent::__construct($element);
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getRegisterIconsClass(): string
    {
        return '\\' . $this->element->getIconRegisterClass() . '::registerIcons(';
    }

    public function copyContentElementDefaultIcon()
    {
        $extensionName = $this->element->getExtensionName();
        $name = $this->element->getName();
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
        $extensionName = $this->element->getExtensionName();
        $staticName = $this->element->getStaticName();
        $name = $this->element->getName();
        if (!file_exists('public/typo3conf/ext/' . $extensionName . '/Resources/Public/Icons/' . $this->element->getElementType())) {
            mkdir('public/typo3conf/ext/' . $extensionName . '/Resources/Public/Icons/' . $this->element->getElementType(), 0777, true);
        }
        copy(
            'public/typo3conf/ext/content_element_registry/Resources/Public/Icons/CEDefaultIcon.svg',
            'public/typo3conf/ext/' . $extensionName . '/Resources/Public/Icons/' . $this->element->getElementType() . '/' .
            str_replace('_', '', $extensionName) . '_' .
            strtolower($this->element->getStaticName()) . '_'.
            strtolower($this->element->getName()) . '.svg'
        );

        GeneralCreateCommandUtility::importStringInToFileAfterString(
            'public/typo3conf/ext/' . $extensionName . '/ext_localconf.php',
            [
                "                '" . $this->element->getElementType() . "/" . str_replace('_', '', $extensionName) . "_" . strtolower($staticName) . "_" . strtolower($name) . "', \n"
            ],
            $this->getRegisterIconsClass(),
            1,
            [
                'newLines' => $this->createNewRegistrationIconsFunction(
                    $this->element->getElementType() . '/' . str_replace('_', '', $extensionName) . '_' . strtolower($staticName) . '_' . strtolower($name)
                    ),
                'universalStringInFile' => 'function ($extKey) {',
                'linesAfterSpecificString' => 0
            ]
        );
    }

    public function copyPageTypeDefaultIcon()
    {
        $extensionName = $this->element->getExtensionName();
        $doktype = $this->element->getDoktype();

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
        $extensionName = $this->element->getExtensionName();
        $pluginName = $this->element->getName();
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
        $extensionName = $this->element->getExtensionName();
        $recordName = $this->element->getName();
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
