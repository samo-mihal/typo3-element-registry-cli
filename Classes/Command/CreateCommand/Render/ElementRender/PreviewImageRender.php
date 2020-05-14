<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

/**
 * Class PreviewImageRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
class PreviewImageRender extends AbstractRender
{
    /**
     * PreviewImage constructor.
     * @param ElementRender $elementRender
     */
    public function __construct(ElementRender $elementRender)
    {
        parent::__construct($elementRender);
    }

    public function copyContentElementDefault()
    {
        $extensionName = $this->elementRender->getElement()->getExtensionName();
        $name = $this->elementRender->getElement()->getName();
        $this->elementRender->getElement()->getOutput()
            ->writeln('<bg=red;options=bold>• Change Content element Preview image.</>');

        copy(
            'public/typo3conf/ext/content_element_registry/Resources/Public/Images/NewContentElement1.png',
            'public/typo3conf/ext/' . $extensionName . '/Resources/Public/Images/ContentElementPreviews/common_' . str_replace('_', '', $extensionName) . '_' . strtolower($name) . '.png'
        );
    }

    public function copyPluginDefault()
    {
        copy(
            'public/typo3conf/ext/content_element_registry/Resources/Public/Images/NewContentElement1.png',
            "public/typo3conf/ext/" . $this->elementRender->getElement()->getMainExtension() . "/Resources/Public/Images/ContentElementPreviews/plugins_".strtolower($this->elementRender->getElement()->getName()).".png"
        );
    }
}
