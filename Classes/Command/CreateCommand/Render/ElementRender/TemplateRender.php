<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

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

    /**
     * @return void
     */
    public function defaultTemplate(): void
    {
        $filename = $this->element->getTemplatePath();
        if (!file_exists($this->element->getTemplateDirPath())) {
            mkdir($this->element->getTemplateDirPath(), 0777, true);
        }
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
        $this->output->writeln('<bg=red;options=bold>• Fill template: ' . $filename . '</>');
    }

    /**
     * @return void
     */
    public function pluginTemplate(): void
    {
        $filename = $this->element->getTemplatePath();
        if (!file_exists($this->element->getTemplateDirPath())) {
            mkdir($this->element->getTemplateDirPath(), 0777, true);
        }
        file_put_contents(
            $filename,
            '<html xmlns="http://www.w3.org/1999/xhtml" lang="en"
      xmlns:f="http://typo3.org/ns/TYPO3/Fluid/ViewHelpers"
      xmlns:v="http://typo3.org/ns/FluidTYPO3/Vhs/ViewHelpers"
      data-namespace-typo3-fluid="true">

<f:layout name="Default" />

<f:section name="Main">

</f:section>

</html>'
        );
        $this->output->writeln('<bg=red;options=bold>• Fill template: ' . $filename . '</>');
    }

    /**
     * @return void
     */
    public function pageTypeTemplate(): void
    {
        $filename = $this->element->getTemplatePath();
        $pageTypeTemplateContent = '<html xmlns="http://www.w3.org/1999/xhtml" lang="en"
      xmlns:f="http://typo3.org/ns/TYPO3/Fluid/ViewHelpers"
      xmlns:v="http://typo3.org/ns/FluidTYPO3/Vhs/ViewHelpers"
      data-namespace-typo3-fluid="true">

<f:alias map="{' . strtolower($this->element->getName()) . ':dwPageType}">

</f:alias>

</html>';

        if ($this->element->isAutoHeader()) {
            $defaultTemplate = $this->element->getDefaultPageTemplatePath();
            $defaultTemplateLines = file($defaultTemplate);
            if (!(in_array('<f:render partial="PageType/{dwPageType.modelName}/Header" optional="1" arguments="{dwPageType:dwPageType}" />', array_map('trim', $defaultTemplateLines)))) {
                $this->importStringRender->importStringInToFileAfterString(
                    $defaultTemplate,
                    ElementObject::FIELDS_TAB . "<f:render partial=\"PageType/{dwPageType.modelName}/Header\" optional=\"1\" arguments=\"{dwPageType:dwPageType}\" /> \n",
                    '<!--TYPO3SEARCH_begin-->',
                    0
                );
            }

            if (!file_exists($this->element->getTemplateDirPath())) {
                mkdir($this->element->getTemplateDirPath(), 0777, true);
            }
            file_put_contents($filename, $pageTypeTemplateContent);
            $this->output->writeln('<bg=red;options=bold>• Fill auto header template: ' . $filename . '</>');
        }
    }
}
