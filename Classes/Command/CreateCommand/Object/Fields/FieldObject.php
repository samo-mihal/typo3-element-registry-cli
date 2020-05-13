<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\Field\ItemObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\Field\ModelDataTypesObject;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class FieldObject
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields
 */
class FieldObject
{
    /**
     * @var bool
     */
    protected $exist = true;

    /**
     * @var bool
     */
    protected $default = false;

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var ModelDataTypesObject
     */
    protected $modelDataTypes = null;

    /**
     * @var bool
     */
    protected $hasModel = true;

    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var array
     */
    protected $importClasses = [];

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var bool
     */
    protected $tCAItemsAllowed = false;

    /**
     * @var bool
     */
    protected $flexFormItemsAllowed = false;

    /**
     * @var bool
     */
    protected $inlineItemsAllowed = false;

    /**
     * @var string
     */
    protected $defaultTitle = '';

    /**
     * @var string
     */
    protected $sqlDataType = '';

    /**
     * @var ObjectStorage<\Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\Field\ItemObject>
     */
    protected $items = null;

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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return ObjectStorage|null
     */
    public function getItems(): ? ObjectStorage
    {
        return $this->items;
    }

    /**
     * @return ItemObject
     */
    public function getFirstItem(): ? ItemObject
    {
        return $this->getItems()[0];
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(? string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getSqlDataType(): ? string
    {
        return $this->sqlDataType;
    }

    /**
     * @param string|null $sqlDataType
     */
    public function setSqlDataType(? string $sqlDataType)
    {
        $this->sqlDataType = $sqlDataType;
    }

    /**
     * @return bool
     */
    public function hasSqlDataType(): bool
    {
        return $this->getSqlDataType() !== null;
    }

    /**
     * @param ObjectStorage $items
     */
    public function setItems(ObjectStorage $items): void
    {
        $this->items = $items;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->default;
    }

    /**
     * @param bool $default
     */
    public function setDefault(bool $default): void
    {
        $this->default = $default;
    }

    /**
     * @return bool
     */
    public function exist(): bool
    {
        return $this->exist;
    }

    /**
     * @return bool
     */
    public function hasItems(): bool
    {
        return $this->items !== null;
    }

    /**
     * @param bool $exist
     */
    public function setExist(bool $exist): void
    {
        $this->exist = $exist;
    }

    /**
     * @return string|null
     */
    public function getDefaultTitle(): ? string
    {
        return $this->defaultTitle;
    }

    /**
     * @param string|null $defaultTitle
     */
    public function setDefaultTitle(? string $defaultTitle): void
    {
        $this->defaultTitle = $defaultTitle;
    }

    /**
     * @return array|null
     */
    public function getImportClasses(): ? array
    {
        return $this->importClasses;
    }

    /**
     * @param array|null $importClasses
     */
    public function setImportClasses(? array $importClasses): void
    {
        $this->importClasses = $importClasses;
    }

    /**
     * @return bool
     */
    public function isTCAItemsAllowed(): bool
    {
        return $this->tCAItemsAllowed;
    }

    /**
     * @param bool $tCAItemsAllowed
     */
    public function setTCAItemsAllowed(bool $tCAItemsAllowed): void
    {
        $this->tCAItemsAllowed = $tCAItemsAllowed;
    }

    /**
     * @return bool
     */
    public function isFlexFormItemsAllowed(): bool
    {
        return $this->flexFormItemsAllowed;
    }

    /**
     * @param bool $flexFormItemsAllowed
     */
    public function setFlexFormItemsAllowed(bool $flexFormItemsAllowed): void
    {
        $this->flexFormItemsAllowed = $flexFormItemsAllowed;
    }

    /**
     * @return bool
     */
    public function isInlineItemsAllowed(): bool
    {
        return $this->inlineItemsAllowed;
    }

    /**
     * @param bool $inlineItemsAllowed
     */
    public function setInlineItemsAllowed(bool $inlineItemsAllowed): void
    {
        $this->inlineItemsAllowed = $inlineItemsAllowed;
    }

    /**
     * @return ModelDataTypesObject
     */
    public function getModelDataTypes(): ModelDataTypesObject
    {
        return $this->modelDataTypes;
    }

    /**
     * @param ModelDataTypesObject $modelDataTypes
     */
    public function setModelDataTypes(ModelDataTypesObject $modelDataTypes): void
    {
        $this->modelDataTypes = $modelDataTypes;
    }

    /**
     * @return bool
     */
    public function hasModel(): bool
    {
        return $this->hasModel;
    }

    /**
     * @param bool|null $hasModel
     */
    public function setHasModel(? bool $hasModel): void
    {
        $this->hasModel = $hasModel;
    }

    /**
     * @return string
     */
    public function getNameInModel(): string
    {
        return str_replace(' ','',lcfirst(ucwords(str_replace('_',' ', $this->getName()))));
    }

    /**
     * @param ElementRender $element
     * @return string
     */
    public function getNameInTranslation(ElementRender $element): string
    {
        $table = $element->getTable();
        if ($element->isTcaFieldsPrefix() == false) {
            return $table . '.' . $this->getName();
        } else {
            return $table . '.' . strtolower($element->getName()) . '_' . $this->getName();
        }
    }

    /**
     * @param ElementRender $element
     * @return string
     */
    public function getNameInTCA(ElementRender $element): string
    {
        if ($this->isDefault()) {
            return $this->getType();
        } elseif ($element->isTcaFieldsPrefix() == false) {
            return $this->getName();
        } else {
            return strtolower($element->getName()) . '_' . $this->getName();
        }
    }
}
