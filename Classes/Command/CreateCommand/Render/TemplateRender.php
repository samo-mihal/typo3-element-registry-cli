<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;
use Symfony\Component\Console\Exception\InvalidArgumentException;

/**
 * Class Template
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render
 */
class TemplateRender
{
    /**
     * @var null
     */
    protected $render = null;

    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
    }

    public function contentElementTemplate()
    {
        file_put_contents(
            'public/typo3conf/ext/' . $this->render->getExtensionName() . '/Resources/Private/Templates/ContentElements/' . $this->render->getName() . '.html',
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
        $controllerName = $this->render->getControllerName();
        $actionName = $this->render->getActionName();
        $extensionName = $this->render->getExtensionName();

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
        $pageTypeName = $this->render->getName();
        $autoHeader = $this->render->isAutoHeader();
        $mainExtension = $this->render->getMainExtension();

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
            $this->render->getOutput()->writeln('<bg=red;options=bold>• Fill auto header template: public/typo3conf/ext/' . $mainExtension . '/Resources/Private/Partials/PageType</>');
        }
    }
}
