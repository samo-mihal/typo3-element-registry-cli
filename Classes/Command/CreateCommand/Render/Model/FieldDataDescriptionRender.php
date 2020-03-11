<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\Model;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\Field\ModelDataTypesObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
/**
 * Class FieldDataDescription
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\TCA
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
     */
    public function getDescription(FieldObject $field)
    {

        if ($field->isDefault()) {
            $result = $this->getDefaultFieldDescription($field);
        } else {
            $fieldType = $field->getType();
            $result = [
                'input' => $fieldType === 'input' ? $this->getStringDescription() : null,
                'select' => $fieldType === 'select' ? $this->getIntDescription() : null,
                'fal' => $fieldType === 'fal' ? $this->getObjectStorageAsFileRefrenceDescription() : null,
                'radio' => $fieldType === 'radio' ? $this->getIntDescription() : null,
                'check' => $fieldType === 'check' ? $this->getIntDescription() : null,
                'textarea' => $fieldType === 'textarea' ? $this->getStringDescription() : null,
                'group' => $fieldType === 'group' ? $this->getObjectStorageDescription() : null,
                'inline' => $fieldType === 'inline' ? $this->getInlineDescription($field) : null,
            ][$fieldType];
        }

        $modelDataTypes = new ModelDataTypesObject();
        $modelDataTypes->setPropertyDataType($result[0]);
        $modelDataTypes->setPropertyDataTypeDescribe($result[1]);
        $modelDataTypes->setGetterDataTypeDescribe($result[2]);
        $modelDataTypes->setGetterDataType($result[3]);

        $field->setModelDataTypes($modelDataTypes);
        return $field;
    }

    /**
     * @return array
     */
    public function getStringDescription(): array
    {
       return [
           '""',
           'string',
           'string',
           'string'
       ];
    }

    /**
     * @return array
     */
    public function getIntDescription(): array
    {
        return [
            '0',
            'int',
            'int',
            '? int'
        ];
    }

    /**
     * @param FieldObject $field
     * @return array
     */
    public function getInlineDescription(FieldObject $field)
    {
        $inlineRelativePath = $this->render->getModelNamespace();

        return [
            'null',
            '\TYPO3\CMS\Extbase\Persistence\ObjectStorage<\\' . $inlineRelativePath . '\\' . $this->render->getName() . '\\' . $field->getFirstItem()->getName() . '>',
            'ObjectStorage',
            '? ObjectStorage'
        ];
    }

    /**
     * @return array
     */
    public function getFileRefrenceDescription()
    {
        return [
            'null',
            '\TYPO3\CMS\Extbase\Domain\Model\FileReference',
            'FileReference',
            '? FileReference'
        ];
    }

    /**
     * @return array
     */
    public function getObjectStorageDescription()
    {
        return [
            'null',
            '\TYPO3\CMS\Extbase\Persistence\ObjectStorage',
            'ObjectStorage',
            '? ObjectStorage'
        ];
    }

    /**
     * @return array
     */
    public function getObjectStorageAsFileRefrenceDescription()
    {
        return [
            'null',
            '\TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>',
            'ObjectStorage',
            '? ObjectStorage'
        ];
    }

    /**
     * @return array
     */
    public function getFlexFormDescription()
    {
        return [
            '""',
            'string',
            'array',
            '? array'
        ];
    }

    /**
     * @param FieldObject $field
     * @return array
     */
    public function getDefaultFieldDescription(FieldObject $field)
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
                    $result = $this->getFileRefrenceDescription();
                } else {
                    $result = $this->getObjectStorageAsFileRefrenceDescription();
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
