<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Utility;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\Typo3FieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\Field\ItemObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\SQLDatabaseRender;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class ElementObject
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
     * @param ObjectStorage $fields
     * @param $table
     * @return bool
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public static function areAllFieldsDefault(ObjectStorage $fields, $table)
    {
        if (!empty($fields)) {
            $TCAFieldTypes = GeneralUtility::makeInstance(Typo3FieldTypesConfig::class)->getTCAFieldTypes($table);

            /** @var FieldObject $field */
            foreach ($fields as $field) {
                $fieldType = $field->getType();

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
    public function getFieldSQLDataType($table, $fieldType, FieldObject $field)
    {
        $TCAFieldTypes = $this->getTCAFieldTypes();

        if ($field->hasItems() && $this->isAllItemsNumeric($field->getItems())) {
            return SQLDatabaseRender::VARCHAR_255;
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
}
