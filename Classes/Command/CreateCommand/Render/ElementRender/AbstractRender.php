<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class AbstractRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
abstract class AbstractRender
{
    /**
     * @var ElementRender
     */
    protected $elementRender = null;

    /**
     * @var ElementObject
     */
    protected $element = null;

    /**
     * @var OutputInterface
     */
    protected $output = null;

    /**
     * @var ObjectStorage
     */
    protected $fields = null;

    /**
     * @var string
     */
    protected $extensionName = '';

    /**
     * @var StandaloneView
     */
    protected $view = null;

    /**
     * Abstract render constructor.
     * @param ElementRender $elementRender
     */
    public function __construct(ElementRender $elementRender)
    {
        $this->elementRender = $elementRender;
        $this->element = $this->elementRender->getElement();
        $this->output = $this->element->getOutput();
        $this->extensionName = $this->element->getExtensionName();
        $this->fields = $this->element->getFields() ?: null;
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
    }
}
