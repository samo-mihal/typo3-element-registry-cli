<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\Field;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;
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
     * @param ElementObject $elementObject
     * @param FieldObject $field
     * @return string
     */
    public function getNameInTranslation(ElementObject $elementObject, FieldObject $field): string
    {
        $table = $elementObject->getTable();
        $extensionName = strtolower($elementObject->getExtensionNameSpaceFormat());
        $name = strtolower($elementObject->getName());

        return $table . '.' . $extensionName . '_' . $name . '.'. $name . '_' . $field->getName() . '.' . $this->getName();
    }

    /**
     * @param ElementObject $elementObject
     * @param FieldObject $field
     * @return string
     */
    public function getConstantPath(ElementObject $elementObject, FieldObject $field): string
    {
        $modelNameSpace = $elementObject->getModelNamespace();
        $name = $elementObject->getName();
        $fieldName = strtoupper($field->getName());
        $itemName = strtoupper($this->getName());

        return '\\' . $modelNameSpace . '\\' . $name . '::' . $fieldName . '_' . $itemName;
    }

    /**
     * @param ElementObject $elementObject
     * @return string
     */
    public function getInlineConstantPath(ElementObject $elementObject): string
    {
        $modelNameSpace = $elementObject->getModelNamespace();
        $name = $elementObject->getName();
        $itemName = strtoupper($this->getName());

        return '\\' . $modelNameSpace . '\\' . $name . '::CONTENT_RELATION_' . $itemName;
    }

    /**
     * @param ElementObject $elementObject
     * @return string
     */
    public function getNewForeignTable(ElementObject $elementObject): string
    {
        $extensionName = strtolower($elementObject->getExtensionNameSpaceFormat());
        $itemName = strtolower($this->getName());

        return 'tx_' . $extensionName . '_domain_model_' . $elementObject->getNamesFromModelPath() . '_' . $itemName;
    }
}
