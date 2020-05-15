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
     * @param ElementRender $elementRender
     */
    public function __construct(ElementRender $elementRender)
    {
        parent::__construct($elementRender);
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getRegisterIconsClass(): string
    {
        return '\\' . $this->elementRender->getElement()->getIconRegisterClass() . '::registerIcons(';
    }

    public function copyContentElementDefaultIcon()
    {
        $extensionName = $this->elementRender->getElement()->getExtensionName();
        $name = $this->elementRender->getElement()->getName();
        $filename = 'public/typo3conf/ext/' . $extensionName . '/Resources/Public/Icons/ContentElement/' .
            str_replace('_', '', $extensionName) . '_' . strtolower($name) . '.svg';
        $this->output->writeln('<bg=red;options=bold>• Change Content element Icon.</>');
        copy(
            'public/typo3conf/ext/content_element_registry/Resources/Public/Icons/CEDefaultIcon.svg',
            $filename
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
        $extensionName = $this->elementRender->getElement()->getExtensionName();
        $staticName = $this->elementRender->getElement()->getStaticName();
        $name = $this->elementRender->getElement()->getName();
        if (!file_exists('public/typo3conf/ext/' . $extensionName . '/Resources/Public/Icons/' . $this->elementRender->getElement()->getType())) {
            mkdir('public/typo3conf/ext/' . $extensionName . '/Resources/Public/Icons/' . $this->elementRender->getElement()->getType(), 0777, true);
        }
        copy(
            'public/typo3conf/ext/content_element_registry/Resources/Public/Icons/CEDefaultIcon.svg',
            'public/typo3conf/ext/' . $extensionName . '/Resources/Public/Icons/' . $this->elementRender->getElement()->getType() . '/' .
            str_replace('_', '', $extensionName) . '_' .
            strtolower($this->elementRender->getElement()->getStaticName()) . '_'.
            strtolower($this->elementRender->getElement()->getName()) . '.svg'
        );

        GeneralCreateCommandUtility::importStringInToFileAfterString(
            'public/typo3conf/ext/' . $extensionName . '/ext_localconf.php',
            [
                "                '" . $this->elementRender->getElement()->getType() . "/" . str_replace('_', '', $extensionName) . "_" . strtolower($staticName) . "_" . strtolower($name) . "', \n"
            ],
            $this->getRegisterIconsClass(),
            1,
            [
                'newLines' => $this->createNewRegistrationIconsFunction(
                    $this->elementRender->getElement()->getType() . '/' . str_replace('_', '', $extensionName) . '_' . strtolower($staticName) . '_' . strtolower($name)
                    ),
                'universalStringInFile' => 'function ($extKey) {',
                'linesAfterSpecificString' => 0
            ]
        );
    }

    public function copyPageTypeDefaultIcon()
    {
        $extensionName = $this->elementRender->getElement()->getExtensionName();
        $doktype = $this->elementRender->getElement()->getDoktype();
        $this->output->writeln('<bg=red;options=bold>• Change PageType Icon.</>');
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
        $extensionName = $this->elementRender->getElement()->getExtensionName();
        $pluginName = $this->elementRender->getElement()->getName();
        $this->output->writeln('<bg=red;options=bold>• Change Plugin Icon.</>');

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
        $extensionName = $this->elementRender->getElement()->getExtensionName();
        $recordName = $this->elementRender->getElement()->getName();
        $this->output->writeln('<bg=red;options=bold>• Change record Icon.</>');
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
