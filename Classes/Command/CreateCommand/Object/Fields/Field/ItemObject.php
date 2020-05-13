<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\Field;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;

/**
 * Class ItemObject
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\Field
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
     * @param ElementRender $element
     * @param FieldObject $field
     * @return string
     */
    public function getNameInTranslation(ElementRender $element, FieldObject $field): string
    {
        $table = $element->getTable();
        $extensionName = strtolower($element->getExtensionNameSpaceFormat());
        $name = strtolower($element->getName());

        return $table . '.' . $extensionName . '_' . $name . '.'. $name . '_' . $field->getName() . '.' . $this->getName();
    }

    /**
     * @param ElementRender $element
     * @param FieldObject $field
     * @return string
     */
    public function getConstantPath(ElementRender $element, FieldObject $field): string
    {
        $modelNameSpace = $element->getModelNamespace();
        $name = $element->getName();
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
        $modelNameSpace = $element->getModelNamespace();
        $name = $element->getName();
        $itemName = strtoupper($this->getName());

        return '\\' . $modelNameSpace . '\\' . $name . '::CONTENT_RELATION_' . $itemName;
    }

    /**
     * @param ElementRender $element
     * @return string
     */
    public function getNewForeignTable(ElementRender $element): string
    {
        $extensionName = strtolower($element->getExtensionNameSpaceFormat());
        $itemName = strtolower($this->getName());

        return 'tx_' . $extensionName . '_domain_model_' . $element->getTcaRelativePath() . '_' . $itemName;
    }
}
