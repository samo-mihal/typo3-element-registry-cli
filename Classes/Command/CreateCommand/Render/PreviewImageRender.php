<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;

/**
 * Class PreviewImage
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render
 */
class PreviewImageRender
{
    /**
     * @var RenderCreateCommand
     */
    protected $render = null;

    /**
     * PreviewImage constructor.
     * @param RenderCreateCommand $render
     */
    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
    }

    public function copyContentElementDefault()
    {
        $extensionName = $this->render->getExtensionName();
        $name = $this->render->getName();
        copy(
            'public/typo3conf/ext/content_element_registry/Resources/Public/Images/NewContentElement1.png',
            'public/typo3conf/ext/' . $extensionName . '/Resources/Public/Images/ContentElementPreviews/common_' . str_replace('_', '', $extensionName) . '_' . strtolower($name) . '.png'
        );
    }

    public function copyPluginDefault()
    {
        copy(
            'public/typo3conf/ext/content_element_registry/Resources/Public/Images/NewContentElement1.png',
            "public/typo3conf/ext/" . $this->render->getMainExtension() . "/Resources/Public/Images/ContentElementPreviews/plugins_".strtolower($this->render->getName()).".png"
        );
    }
}
