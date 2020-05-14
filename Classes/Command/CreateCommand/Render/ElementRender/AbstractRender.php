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
    protected $elementRender = null;

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
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
    }
}
