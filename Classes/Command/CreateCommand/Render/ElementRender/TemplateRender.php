<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;

/**
 * Class TemplateRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
class TemplateRender extends AbstractRender
{
    /**
     * TemplateRender constructor.
     * @param ElementRender $elementRender
     */
    public function __construct(ElementRender $elementRender)
    {
        parent::__construct($elementRender);
    }

    public function contentElementTemplate()
    {
        $filename = 'public/typo3conf/ext/' . $this->elementRender->getElement()->getExtensionName() .
            '/Resources/Private/Templates/ContentElements/' . $this->elementRender->getElement()->getName() . '.html';
        $this->output->writeln('<bg=red;options=bold>• Fill template: ' . $filename . '</>');
        file_put_contents(
            $filename,
            '<html xmlns="http://www.w3.org/1999/xhtml" lang="en"
      xmlns:f="http://typo3.org/ns/TYPO3/Fluid/ViewHelpers"
      xmlns:v="http://typo3.org/ns/FluidTYPO3/Vhs/ViewHelpers"
      data-namespace-typo3-fluid="true">

<f:layout name="ContentElements/{contentElement.layout}" />

<f:section name="Main">
</f:section>

<f:section name="Preview">
</f:section>

</html>'
        );
    }

    public function pluginTemplate()
    {
        $controllerName = $this->elementRender->getElement()->getControllerName();
        $actionName = $this->elementRender->getElement()->getActionName();
        $extensionName = $this->elementRender->getElement()->getExtensionName();
        $filename = 'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Templates/' . $controllerName . '/' . ucfirst($actionName) . '.html';
        $this->output->writeln('<bg=red;options=bold>• Fill template: '. $filename . '</>');

        if (!file_exists('public/typo3conf/ext/' . $extensionName . '/Resources/Private/Templates/' . $controllerName)) {
            mkdir('public/typo3conf/ext/' . $extensionName . '/Resources/Private/Templates/' . $controllerName, 0777, true);
        }

        file_put_contents(
            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Templates/' . $controllerName . '/' . ucfirst($actionName) . '.html',
            '<html xmlns="http://www.w3.org/1999/xhtml" lang="en"
      xmlns:f="http://typo3.org/ns/TYPO3/Fluid/ViewHelpers"
      xmlns:v="http://typo3.org/ns/FluidTYPO3/Vhs/ViewHelpers"
      data-namespace-typo3-fluid="true">

<f:layout name="Default" />

<f:section name="Main">

</f:section>

</html>'
        );
    }

    public function pageTypeTemplate()
    {
        $pageTypeName = $this->elementRender->getElement()->getName();
        $autoHeader = $this->elementRender->getElement()->isAutoHeader();
        $mainExtension = $this->elementRender->getElement()->getMainExtension();

        $pageTypeTemplate = 'public/typo3conf/ext/' . $mainExtension . '/Resources/Private/Partials/PageType/' . $pageTypeName . '/Header.html';
        $pageTypeTemplateContent = '<html xmlns="http://www.w3.org/1999/xhtml" lang="en"
      xmlns:f="http://typo3.org/ns/TYPO3/Fluid/ViewHelpers"
      xmlns:v="http://typo3.org/ns/FluidTYPO3/Vhs/ViewHelpers"
      data-namespace-typo3-fluid="true">

<f:alias map="{' . strtolower($pageTypeName) . ':dwPageType}">

</f:alias>

</html>';

        if ($autoHeader) {
            $defaultTemplate = 'public/typo3conf/ext/' . $mainExtension . '/Resources/Private/Templates/Page/Default.html';
            $defaultTemplateLines = file($defaultTemplate);
            if (!(in_array('<f:render partial="PageType/{dwPageType.modelName}/Header" optional="1" arguments="{dwPageType:dwPageType}" />', array_map('trim', $defaultTemplateLines))))
            {
                GeneralCreateCommandUtility::importStringInToFileAfterString(
                    $defaultTemplate,
                    ["    <f:render partial=\"PageType/{dwPageType.modelName}/Header\" optional=\"1\" arguments=\"{dwPageType:dwPageType}\" /> \n"],
                    '<!--TYPO3SEARCH_begin-->',
                    0
                );
            }

            if (!file_exists('public/typo3conf/ext/' . $mainExtension . '/Resources/Private/Partials/PageType')) {
                mkdir('public/typo3conf/ext/' . $mainExtension . '/Resources/Private/Partials/PageType', 0777, true);
            }
            if (!file_exists('public/typo3conf/ext/' . $mainExtension . '/Resources/Private/Partials/PageType/' . $pageTypeName)) {
                mkdir('public/typo3conf/ext/' . $mainExtension . '/Resources/Private/Partials/PageType/' . $pageTypeName, 0777, true);
            }
            file_put_contents($pageTypeTemplate, $pageTypeTemplateContent);
            $this->output->writeln('<bg=red;options=bold>• Fill auto header template: public/typo3conf/ext/' . $mainExtension . '/Resources/Private/Partials/PageType</>');
        }
    }
}
