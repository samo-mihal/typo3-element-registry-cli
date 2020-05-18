<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ControllerRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
class ControllerRender extends AbstractRender
{
    /**
     * ControllerRender constructor.
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
    public function template()
    {
        if (!file_exists($this->element->getControllerPath())) {
            mkdir($this->element->getControllerDirPath(), 0777, true);
            $view = clone $this->view;
            $view->setTemplatePathAndFilename(
                GeneralUtility::getFileAbsFileName(
                    'EXT:typo3_element_registry_cli/Resources/Private/Templates/Controller/ControllerTemplate.html'
                )
            );
            $view->assignMultiple([
                'vendor' => $this->element->getVendor(),
                'name' => $this->element->getControllerName(),
                'extensionNameInNamespaceFormat' => $this->element->getExtensionNameSpaceFormat(),
                'pluginControllerExtendClass' => $this->element->getPluginControllerExtendClass(),
                'endOfPluginControllerExtendClass' => end(
                    explode('\\', $this->element->getPluginControllerExtendClass())
                )
            ]);

            file_put_contents(
                $this->element->getControllerPath(),
                $view->render()
            );
        }

        if ($this->element->getActionName()) {
            $view = clone $this->view;
            $view->setTemplatePathAndFilename(
                GeneralUtility::getFileAbsFileName(
                    'EXT:typo3_element_registry_cli/Resources/Private/Templates/Controller/ControllerAction.html'
                )
            );
            $view->assignMultiple([
                'name' => $this->element->getActionName(),
            ]);
            $this->importStringRender->importStringInToFileAfterString(
                $this->element->getControllerPath(),
                $view->render(),
                "{",
                0
            );
        }
    }
}
