<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\Field;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\FieldObject;

/**
 * Class ItemObject
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\Field
 */
class ItemObject
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $value = '';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->getValue();
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @param ElementRender $elementRender
     * @param FieldObject $field
     * @return string
     */
    public function getNameInTranslation(ElementRender $elementRender, FieldObject $field): string
    {
        $table = $elementRender->getElement()->getTable();
        $extensionName = strtolower($elementRender->getElement()->getExtensionNameSpaceFormat());
        $name = strtolower($elementRender->getElement()->getName());

        return $table . '.' . $extensionName . '_' . $name . '.'. $name . '_' . $field->getName() . '.' . $this->getName();
    }

    /**
     * @param ElementRender $element
     * @param FieldObject $field
     * @return string
     */
    public function getConstantPath(ElementRender $element, FieldObject $field): string
    {
        $modelNameSpace = $element->getElement()->getModelNamespace();
        $name = $element->getElement()->getName();
        $fieldName = strtoupper($field->getName());
        $itemName = strtoupper($this->getName());

        return '\\' . $modelNameSpace . '\\' . $name . '::' . $fieldName . '_' . $itemName;
    }

    /**
     * @param ElementRender $element
     * @param FieldObject $field
     * @return string
     */
    public function getInlineConstantPath(ElementRender $element, FieldObject $field): string
    {
        $modelNameSpace = $element->getElement()->getModelNamespace();
        $name = $element->getElement()->getName();
        $itemName = strtoupper($this->getName());

        return '\\' . $modelNameSpace . '\\' . $name . '::CONTENT_RELATION_' . $itemName;
    }

    /**
     * @param ElementRender $elementRender
     * @return string
     */
    public function getNewForeignTable(ElementRender $elementRender): string
    {
        $extensionName = strtolower($elementRender->getElement()->getExtensionNameSpaceFormat());
        $itemName = strtolower($this->getName());

        return 'tx_' . $extensionName . '_domain_model_' . $elementRender->getElement()->getTcaRelativePath() . '_' . $itemName;
    }
}
