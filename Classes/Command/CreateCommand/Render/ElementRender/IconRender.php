<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

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
        copy(
            $this->element->getDefaultIconPath(),
            $this->element->getIconPath()
        );
        $this->output->writeln('<bg=red;options=bold>• Change Content element Icon.</>');
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
        $staticType = str_replace(' ', '', ucwords($this->elementRender->getElement()->getStaticType()));
        if (!file_exists($this->element->getIconDirPath())) {
            mkdir($this->element->getIconDirPath(), 0777, true);
        }
        copy(
            $this->element->getDefaultIconPath(),
            $this->element->getIconPath()
        );
        $this->importStringRender->importStringInToFileAfterString(
            $this->element->getExtLocalConfPath(),
            ElementObject::FIELDS_TAB . ElementObject::FIELDS_TAB . ElementObject::FIELDS_TAB .
            ElementObject::FIELDS_TAB . "'" . $staticType . "/" .
            strtolower($this->element->getExtensionNameSpaceFormat()) .
            "_" . strtolower($this->element->getNamesFromModelPath()) . "', \n",
            $this->getRegisterIconsClass(),
            1,
            [
                'newLines' => $this->createNewRegistrationIconsFunction(
                    $staticType . '/' . strtolower($this->element->getExtensionNameSpaceFormat()) .
                    '_' . strtolower($this->element->getNamesFromModelPath())
                ),
                'universalStringInFile' => 'function ($extKey) {',
                'linesAfterSpecificString' => 0
            ]
        );
    }

    public function copyPageTypeDefaultIcon()
    {
        copy(
            $this->element->getDefaultIconPath(),
            $this->element->getIconPath()['inMenu']
        );
        copy(
            $this->element->getDefaultIconPath(),
            $this->element->getIconPath()['notInMenu']
        );
        $this->output->writeln('<bg=red;options=bold>• Change PageType Icon.</>');
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function copyElementDefaultIcon()
    {
        $name = $this->elementRender->getElement()->getName();
        copy(
            $this->element->getDefaultIconPath(),
            $this->element->getIconPath()
        );

        $this->importStringRender->importStringInToFileAfterString(
            $this->element->getExtLocalConfPath(),
            ElementObject::FIELDS_TAB . ElementObject::FIELDS_TAB . ElementObject::FIELDS_TAB .
            ElementObject::FIELDS_TAB . "'" . $name . "',\n",
            $this->getRegisterIconsClass(),
            1,
            [
                'newLines' => $this->createNewRegistrationIconsFunction($name),
                'universalStringInFile' => 'function ($extKey) {',
                'linesAfterSpecificString' => 0
            ]
        );
        $this->output->writeln('<bg=red;options=bold>• Change ' . $this->element->getType() . ' Icon.</>');
    }
}
