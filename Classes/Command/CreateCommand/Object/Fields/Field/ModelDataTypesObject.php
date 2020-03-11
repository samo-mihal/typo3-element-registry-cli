<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\Field;

/**
 * Class ModelDataTypesObject
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\Field
 */
class ModelDataTypesObject
{
    /**
     * @var string
     */
    protected $propertyDataType = '';

    /**
     * @var string
     */
    protected $propertyDataTypeDescribe = '';

    /**
     * @var string
     */
    protected $getterDataTypeDescribe = '';

    /**
     * @var string
     */
    protected $getterDataType = '';

    /**
     * @return string
     */
    public function getPropertyDataType(): string
    {
        return $this->propertyDataType;
    }

    /**
     * @param string $propertyDataType
     */
    public function setPropertyDataType(string $propertyDataType): void
    {
        $this->propertyDataType = $propertyDataType;
    }

    /**
     * @return string
     */
    public function getPropertyDataTypeDescribe(): string
    {
        return $this->propertyDataTypeDescribe;
    }

    /**
     * @param string $propertyDataTypeDescribe
     */
    public function setPropertyDataTypeDescribe(string $propertyDataTypeDescribe): void
    {
        $this->propertyDataTypeDescribe = $propertyDataTypeDescribe;
    }

    /**
     * @return string
     */
    public function getGetterDataTypeDescribe(): string
    {
        return $this->getterDataTypeDescribe;
    }

    /**
     * @param string $getterDataTypeDescribe
     */
    public function setGetterDataTypeDescribe(string $getterDataTypeDescribe): void
    {
        $this->getterDataTypeDescribe = $getterDataTypeDescribe;
    }

    /**
     * @return string
     */
    public function getGetterDataType(): string
    {
        return $this->getterDataType;
    }

    /**
     * @param string $getterDataType
     */
    public function setGetterDataType(string $getterDataType): void
    {
        $this->getterDataType = $getterDataType;
    }
}
