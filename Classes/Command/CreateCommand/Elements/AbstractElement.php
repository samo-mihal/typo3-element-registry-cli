<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class PageType
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements
 */
abstract class AbstractElement
{
    /**
     * @var ElementRender
     */
    protected $elementRender = null;

    /**
     * AbstractElement constructor.
     */
    public function __construct()
    {
        $this->elementRender = GeneralUtility::makeInstance(ElementRender::class);
    }
}
