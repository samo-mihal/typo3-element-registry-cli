<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Utility;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\Typo3FieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\Field\ItemObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\FieldsObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\SQLDatabaseRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class FieldsObject
 * @package Digitalwerk\Typo3ElementRegistryCli\Utility
 */
class FieldsCreateCommandUtility
{
    /**
     * @var array
     */
    protected $TCAFieldTypes = [];

    /**
     * @return array
     */
    public function getTCAFieldTypes(): array
    {
        return $this->TCAFieldTypes;
    }

    /**
     * @param array $TCAFieldTypes
     */
    public function setTCAFieldTypes(array $TCAFieldTypes): void
    {
        $this->TCAFieldTypes = $TCAFieldTypes;
    }

    /**
     * @param $fields
     * @param $table
     * @return bool
     */
    public function areAllFieldsDefault($fields, $table)
    {
        if (!empty($fields)) {
            $TCAFieldTypes = $this->getTCAFieldTypes();

            foreach ($fields as $field) {
                $fieldType = explode(',', $field)[1];

                if ($TCAFieldTypes[$table][$fieldType]['isFieldDefault'] === true) {
                } elseif ($TCAFieldTypes[$table][$fieldType]['isFieldDefault'] === false) {

                    return false;
                    break;
                }
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $field
     * @return string
     */
    public function getFieldName($field)
    {
        return explode(',', $field)[0];
    }

    /**
     * @param $field
     * @return string
     */
    public function getFieldType($field)
    {
        return explode(',', $field)[1];
    }

    /**
     * @param $field
     * @return string
     */
    public function getFieldTitle($field)
    {
        return explode(',', $field)[2];
    }

    /**
     * @param $field
     * @return array
     */
    public function getFieldItems($field)
    {
        $fieldItems = explode('*', explode(',', $field)[3]);
        array_pop($fieldItems);
        return $fieldItems;
    }

    /**
     * @param $field
     * @return string
     */
    public function getFirstFieldItem($field)
    {
        return explode('*', explode(',', $field)[3])[0];
    }

    /**
     * @param $field
     * @return bool
     */
    public function hasItems($field)
    {
        return !empty(self::getFieldItems($field));
    }

    /**
     * @param $item
     * @return string
     */
    public function getItemName($item)
    {
        return explode(';', $item)[0];
    }

    /**
     * @param $item
     * @return string
     */
    public function getItemType($item)
    {
        return explode(';', $item)[1];
    }

    /**
     * @param $item
     * @return string
     */
    public function getItemValue($item)
    {
        return explode(';', $item)[1];
    }

    /**
     * @param $item
     * @return string
     */
    public function getItemTitle($item)
    {
        return explode(';', $item)[2];
    }

    /**
     * @param $table
     * @param $fieldType
     * @return bool
     */
    public function isFieldTypeDefault($table, $fieldType)
    {
        $TCAFieldTypes = $this->getTCAFieldTypes();
        return $TCAFieldTypes[$table][$fieldType]['isFieldDefault'] === true;
    }

    /**
     * @param $table
     * @param $fieldType
     * @return mixed
     */
    public function getFieldDefaultTitle($table, $fieldType)
    {
        $TCAFieldTypes = $this->getTCAFieldTypes();
        return $TCAFieldTypes[$table][$fieldType]['defaultFieldTitle'];
    }

    /**
     * @param $table
     * @param $fieldType
     * @return bool
     */
    public function isFieldTCAItemsAllowed($table, $fieldType)
    {
        $TCAFieldTypes = $this->getTCAFieldTypes();
        return $TCAFieldTypes[$table][$fieldType]['TCAItemsAllowed'] === true;
    }

    /**
     * @param $table
     * @param $fieldType
     * @return bool
     */
    public function isFlexFormTCAItemsAllowed($table, $fieldType)
    {
        $TCAFieldTypes = $this->getTCAFieldTypes();
        return $TCAFieldTypes[$table][$fieldType]['FlexFormItemsAllowed'] === true;
    }

    /**
     * @param $table
     * @param $fieldType
     * @return bool
     */
    public function isFieldInlineItemsAllowed($table, $fieldType)
    {
        $TCAFieldTypes = $this->getTCAFieldTypes();
        return $TCAFieldTypes[$table][$fieldType]['inlineFieldsAllowed'] === true;
    }

    /**
     * @param $table
     * @param $fieldType
     * @return bool
     */
    public function hasNotFieldModel($table, $fieldType)
    {
        $TCAFieldTypes = $this->getTCAFieldTypes();
        return $TCAFieldTypes[$table][$fieldType]['hasModel'] === false;
    }

    /**
     * @param $table
     * @param $fieldType
     * @return mixed
     */
    public function getFieldImportClasses($table, $fieldType)
    {
        $TCAFieldTypes = $this->getTCAFieldTypes();
        return $TCAFieldTypes[$table][$fieldType]['importClass'];
    }

    /**
     * @param $items
     * @return mixed
     */
    protected function isAllItemsNumeric($items)
    {
        /** @var ItemObject $item */
        foreach ($items as $item) {
            if (!is_numeric($item->getValue())) {
                return false;
                break;
            }
        }

        return true;
    }

    /**
     * @param $table
     * @param $fieldType
     * @param FieldObject $field
     * @return mixed
     */
    protected function getFieldSQLDataType($table, $fieldType, FieldObject $field)
    {
        $TCAFieldTypes = $this->getTCAFieldTypes();

        if ($field->hasItems() && $this->isAllItemsNumeric($field->getItems())) {
            return GeneralUtility::makeInstance(SQLDatabaseRender::class, new ElementRender())->getVarchar255DataType();
        } else {
            return $TCAFieldTypes[$table][$fieldType]['tableFieldDataType'];
        }
    }

    /**
     * @param $table
     * @param $fieldType
     * @return bool
     */
    public function isFieldTypeExist($table, $fieldType)
    {
        $TCAFieldTypes = $this->getTCAFieldTypes();
        return !empty($TCAFieldTypes[$table][$fieldType]);
    }

    /**
     * @param $fields
     * @return array
     * Return converted fields from string to array
     */
    public static function fieldsToArray($fields)
    {
        $fieldsToArray = explode('/',$fields);
        array_pop($fieldsToArray);

        if (count($fieldsToArray) === 0 && $fields !== '-') {
            throw new InvalidArgumentException('Field syntax error.2');
        }

        foreach ($fieldsToArray as $field) {
            if (count(explode(',', $field)) !== 3) {
                if (count(explode(',', $field)) === 4 && count(explode(';', (new FieldsCreateCommandUtility)->getFirstFieldItem($field))) !== 3) {
                    throw new InvalidArgumentException('Field syntax error.');
                }
                if (count(explode(',', $field)) > 4) {
                    throw new InvalidArgumentException('Field syntax error.');
                }
            }
        }

        return $fieldsToArray;
    }

    /**
     * @param $table
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function inicializeTCAFieldTypes($table)
    {
        $this->setTCAFieldTypes(
            GeneralUtility::makeInstance(Typo3FieldTypesConfig::class)->getTCAFieldTypes($table)
        );
    }

    /**
     * @param $fields
     * @param $table
     * @return FieldsObject|null
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function generateObject($fields, $table)
    {
        $this->inicializeTCAFieldTypes($table);
        if ($fields !== '-' && !empty($fields)) {
            $fields = self::fieldsToArray($fields);
            $fieldObjectStorage = new ObjectStorage();

            foreach ($fields as $field) {
                $fieldToObject = new FieldObject();
                $itemObjectStorage = new ObjectStorage();
                $fieldToObject->setName(self::getFieldName($field));
                $fieldToObject->setType(self::getFieldType($field));
                $fieldToObject->setTitle(self::getFieldTitle($field));
                $fieldToObject->setDefault(self::isFieldTypeDefault($table, self::getFieldType($field)));
                $fieldToObject->setExist(self::isFieldTypeExist($table, self::getFieldType($field)));
                $fieldToObject->setDefaultTitle(self::getFieldDefaultTitle($table, self::getFieldType($field)));
                $fieldToObject->setImportClasses(self::getFieldImportClasses($table, self::getFieldType($field)));
                $fieldToObject->setTCAItemsAllowed(self::isFieldTCAItemsAllowed($table, self::getFieldType($field)));
                $fieldToObject->setFlexFormItemsAllowed(self::isFlexFormTCAItemsAllowed($table, self::getFieldType($field)));
                $fieldToObject->setInlineItemsAllowed(self::isFieldInlineItemsAllowed($table, self::getFieldType($field)));
                if (self::hasNotFieldModel($table, self::getFieldType($field))) {
                    $fieldToObject->setHasModel(false);
                }

                if ($this->hasItems($field)) {
                    foreach ($this->getFieldItems($field) as $item) {
                        $itemToObject = new ItemObject();
                        $itemToObject->setName($this->getItemName($item));
                        $itemToObject->setValue($this->getItemValue($item));
                        $itemToObject->setTitle($this->getItemTitle($item));

                        $itemObjectStorage->attach($itemToObject);
                    }
                    $fieldToObject->setItems($itemObjectStorage);
                }

                $fieldToObject->setSqlDataType($this->getFieldSQLDataType($table, $this->getFieldType($field), $fieldToObject));
                $fieldObjectStorage->attach($fieldToObject);
            }

            $fieldsToObject = new FieldsObject();
            $fieldsToObject->setAreDefault(self::areAllFieldsDefault($fields, $table));
            $fieldsToObject->setFields($fieldObjectStorage);

            return $fieldsToObject;
        } else {
            return null;
        }
    }
}
