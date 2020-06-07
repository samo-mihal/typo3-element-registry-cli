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

    /**
     * @return void
     */
    public function copyDefault(): void
    {
        copy(
            $this->element->getDefaultPreviewPath(),
            $this->element->getPreviewPath()
        );
        $this->output->writeln(
            '<bg=red;options=bold>• Change ' . $this->element->getType() . ' Preview image.</>'
        );
    }
}
