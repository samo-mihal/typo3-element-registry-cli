<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;
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
     * @var ElementObject
     */
    protected $elementObject = null;

    /**
     * AbstractElement constructor.
     * @param ElementObject $elementObject
     */
    public function __construct(ElementObject $elementObject)
    {
        $this->elementRender = GeneralUtility::makeInstance(ElementRender::class);
        $elementObject->setModelNamespace();
        $elementObject->setModelPath();
        $elementObject->setStaticType($elementObject->getType());
        $elementObject->setStaticName($elementObject->getName());
        $this->elementObject = $elementObject;
    }
}
