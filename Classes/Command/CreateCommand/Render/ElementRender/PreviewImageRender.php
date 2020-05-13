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
     * @param ElementRender $element
     */
    public function __construct(ElementRender $element)
    {
        parent::__construct($element);
    }

    public function copyContentElementDefault()
    {
        $extensionName = $this->element->getExtensionName();
        $name = $this->element->getName();
        copy(
            'public/typo3conf/ext/content_element_registry/Resources/Public/Images/NewContentElement1.png',
            'public/typo3conf/ext/' . $extensionName . '/Resources/Public/Images/ContentElementPreviews/common_' . str_replace('_', '', $extensionName) . '_' . strtolower($name) . '.png'
        );
    }

    public function copyPluginDefault()
    {
        copy(
            'public/typo3conf/ext/content_element_registry/Resources/Public/Images/NewContentElement1.png',
            "public/typo3conf/ext/" . $this->element->getMainExtension() . "/Resources/Public/Images/ContentElementPreviews/plugins_".strtolower($this->element->getName()).".png"
        );
    }
}
