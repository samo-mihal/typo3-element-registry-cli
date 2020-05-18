<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\Fields\Field;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\Field\ModelDataTypesObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\AbstractRender;
use InvalidArgumentException;

/**
 * Class DataDescriptionRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\Element\Field
 */
class DataDescriptionRender extends AbstractRender
{
    /**
     * @var FieldObject
     */
    protected $field = null;

    /**
     * Data description constructor.
     * @param ElementRender $elementRender
     * @param FieldObject $field
     */
    public function __construct(ElementRender $elementRender, FieldObject $field)
    {
        parent::__construct($elementRender);
        $this->field = $field;
    }

    /**
     * @return FieldObject
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getDescription()
    {
        $field = $this->field;
        if ($field->isDefault()) {
            $result = $this->getDefaultFieldDescription($field);
        } else {
            $fieldType = $field->getType();
            $createCommandCustomData = $this->elementRender->getElement()->getCreateCommandCustomData();
            $newFieldsModelDescription = $createCommandCustomData->newTcaFieldsModelDescription($field);

            $result = [
                'input' => $fieldType === 'input' ? $this->getStringDescription() : null,
                'select' => $fieldType === 'select' ? $this->getIntDescription() : null,
                'fal' => $fieldType === 'fal' ? $this->getObjectStorageAsFileReferenceDescription() : null,
                'radio' => $fieldType === 'radio' ? $this->getIntDescription() : null,
                'check' => $fieldType === 'check' ? $this->getIntDescription() : null,
                'textarea' => $fieldType === 'textarea' ? $this->getStringDescription() : null,
                'group' => $fieldType === 'group' ? $this->getObjectStorageDescription() : null,
                'inline' => $fieldType === 'inline' ? $this->getInlineDescription($field) : null,
            ];

            $result = $newFieldsModelDescription ? array_merge($newFieldsModelDescription, $result) : $result;
            $result = $result[$fieldType];
        }

        $field->setModelDataTypes($result);
        return $field;
    }

    /**
     * @return ModelDataTypesObject
     */
    public function getStringDescription(): ModelDataTypesObject
    {
        $modelDataTypes = new ModelDataTypesObject();
        $modelDataTypes->setPropertyDataType('""');
        $modelDataTypes->setPropertyDataTypeDescribe('string');
        $modelDataTypes->setGetterDataTypeDescribe('string');
        $modelDataTypes->setGetterDataType('string');

        return $modelDataTypes;
    }

    /**
     * @return ModelDataTypesObject
     */
    public function getIntDescription(): ModelDataTypesObject
    {
        $modelDataTypes = new ModelDataTypesObject();
        $modelDataTypes->setPropertyDataType('0');
        $modelDataTypes->setPropertyDataTypeDescribe('int');
        $modelDataTypes->setGetterDataTypeDescribe('int');
        $modelDataTypes->setGetterDataType('? int');

        return $modelDataTypes;
    }

    /**
     * @param FieldObject $field
     * @return ModelDataTypesObject
     */
    public function getInlineDescription(FieldObject $field)
    {
        $inlineRelativePath = $this->elementRender->getElement()->getModelNamespace();
        $modelDataTypes = new ModelDataTypesObject();
        $modelDataTypes->setPropertyDataType('null');
        $modelDataTypes->setPropertyDataTypeDescribe(
            '\TYPO3\CMS\Extbase\Persistence\ObjectStorage<\\' . $inlineRelativePath . '\\' . $this->elementRender->getElement()->getName() . '\\' . $field->getFirstItem()->getName() . '>'
        );
        $modelDataTypes->setGetterDataTypeDescribe('ObjectStorage');
        $modelDataTypes->setGetterDataType('? ObjectStorage');

        return $modelDataTypes;
    }

    /**
     * @return ModelDataTypesObject
     */
    public function getFileReferenceDescription(): ModelDataTypesObject
    {
        $modelDataTypes = new ModelDataTypesObject();
        $modelDataTypes->setPropertyDataType('null');
        $modelDataTypes->setPropertyDataTypeDescribe(
            '\TYPO3\CMS\Extbase\Domain\Model\FileReference'
        );
        $modelDataTypes->setGetterDataTypeDescribe('FileReference');
        $modelDataTypes->setGetterDataType('? FileReference');

        return $modelDataTypes;
    }

    /**
     * @return ModelDataTypesObject
     */
    public function getObjectStorageDescription(): ModelDataTypesObject
    {
        $modelDataTypes = new ModelDataTypesObject();
        $modelDataTypes->setPropertyDataType('null');
        $modelDataTypes->setPropertyDataTypeDescribe(
            '\TYPO3\CMS\Extbase\Persistence\ObjectStorage'
        );
        $modelDataTypes->setGetterDataTypeDescribe('ObjectStorage');
        $modelDataTypes->setGetterDataType('? ObjectStorage');

        return $modelDataTypes;
    }

    /**
     * @return ModelDataTypesObject
     */
    public function getObjectStorageAsFileReferenceDescription(): ModelDataTypesObject
    {
        $modelDataTypes = new ModelDataTypesObject();
        $modelDataTypes->setPropertyDataType('null');
        $modelDataTypes->setPropertyDataTypeDescribe(
            '\TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>'
        );
        $modelDataTypes->setGetterDataTypeDescribe('ObjectStorage');
        $modelDataTypes->setGetterDataType('? ObjectStorage');

        return $modelDataTypes;
    }

    /**
     * @return ModelDataTypesObject
     */
    public function getFlexFormDescription(): ModelDataTypesObject
    {
        $modelDataTypes = new ModelDataTypesObject();
        $modelDataTypes->setPropertyDataType('""');
        $modelDataTypes->setPropertyDataTypeDescribe(
            'string'
        );
        $modelDataTypes->setGetterDataTypeDescribe('array');
        $modelDataTypes->setGetterDataType('? array');

        return $modelDataTypes;
    }

    /**
     * @param FieldObject $field
     * @return ModelDataTypesObject|string
     */
    public function getDefaultFieldDescription(FieldObject $field): ModelDataTypesObject
    {
        $table = $this->elementRender->getElement()->getTable();
        $fieldType = $field->getType();
        $defaultField = $GLOBALS['TCA'][$table]['columns'][$fieldType]['config'];

        if ($defaultField['type'] === 'inline') {
            if ($defaultField['foreign_table_field'] !== 'tablenames') {
                $result = $this->getInlineDescription($field);
            } else {
                if ($defaultField['maxitems'] === 1) {
                    $result = $this->getFileReferenceDescription();
                } else {
                    $result = $this->getObjectStorageAsFileReferenceDescription();
                }
            }
        } elseif ($defaultField['type'] === 'group') {
            $result = $this->getObjectStorageDescription();
        } elseif ($defaultField['type'] === 'flex') {
            $result = $this->getFlexFormDescription();
        } elseif ($defaultField['type'] === 'text' || $defaultField['type'] === 'input') {
            $result = $this->getStringDescription();
        } elseif (
            $defaultField['type'] === 'select' ||
            $defaultField['type'] === 'radio' ||
            $defaultField['type'] === 'check'
        ) {
            $result = $this->getIntDescription();
        } else {
            throw new InvalidArgumentException('Field ' . $field->getName() . ' is not default.');
        }

        return $result;
    }
}
