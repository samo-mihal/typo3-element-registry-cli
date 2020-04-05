<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\Fields;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\Field\ModelDataTypesObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FieldDataDescriptionRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\Fields
 */
class FieldDataDescriptionRender
{
    /**
     * @var null
     */
    protected $render = null;

    /**
     * TCA constructor.
     * @param RenderCreateCommand $render
     */
    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
    }

    /**
     * @param FieldObject $field
     * @return FieldObject
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getDescription(FieldObject $field)
    {
        if ($field->isDefault()) {
            $result = $this->getDefaultFieldDescription($field);
        } else {
            $fieldType = $field->getType();
            $createCommandCustomData = $this->render->getCreateCommandCustomData();
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
        $inlineRelativePath = $this->render->getModelNamespace();
        $modelDataTypes = new ModelDataTypesObject();
        $modelDataTypes->setPropertyDataType('null');
        $modelDataTypes->setPropertyDataTypeDescribe(
            '\TYPO3\CMS\Extbase\Persistence\ObjectStorage<\\' . $inlineRelativePath . '\\' . $this->render->getName() . '\\' . $field->getFirstItem()->getName() . '>'
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
        $table = $this->render->getTable();
        $fieldType = $field->getType();
        $defaultField = $GLOBALS['TCA'][$table]['columns'][$fieldType]['config'];
        $result = '';

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
        }

        return $result;
    }
}
