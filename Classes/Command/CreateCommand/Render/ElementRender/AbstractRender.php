<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
    protected $element = null;

    /**
     * @var StandaloneView
     */
    protected $view = null;

    /**
     * @var GeneralUtility
     */
    protected $generalUtility = null;

    /**
     * Abstract render constructor.
     * @param ElementRender $element
     */
    public function __construct(ElementRender $element)
    {
        $this->element = $element;
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
        $this->generalUtility = new GeneralUtility();
    }
}
