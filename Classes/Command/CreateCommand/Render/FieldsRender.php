<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\FieldsObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\Fields\FieldConfigRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\Fields\FieldRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use InvalidArgumentException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FieldsRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render
 */
class FieldsRender
{
    /**
     * @var null
     */
    protected $render = null;

    /**
     * @var FieldRender
     */
    protected $fieldRender = null;

    /**
     * @var FieldRender
     */
    protected $fieldConfigRender = null;

    /**
     * @var FieldsObject
     */
    protected $fields = null;

    /**
     * TCA constructor.
     * @param RenderCreateCommand $render
     */
    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
        $this->fieldRender = GeneralUtility::makeInstance(FieldRender::class, $render);
        $this->fieldConfigRender = GeneralUtility::makeInstance(FieldConfigRender::class, $render);
        $this->fields = $this->render->getFields();
    }

    /**
     * @return string|null
     */
    public function fieldsToPalette()
    {
        if ($this->fields) {
            $createdFields = [];

            /** @var FieldObject  $field */
            foreach ($this->fields->getFields() as $field) {
                if ($field->exist()) {
                    $createdFields[] = '--linebreak--, ' . $this->fieldRender->fieldNameInTca($field);
                } else {
                    throw new InvalidArgumentException('Field "' . $field->getType() . '" does not exist.1');
                }
            }
            return preg_replace('/--linebreak--, /', '',
                implode(
                    ",\n" . $this->fields->getSpacesInTcaPalette(),
                    $createdFields
                ),
                1
            );
        } else {
            return null;
        }
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function fieldsToColumnsOverrides()
    {
        if ($this->fields) {
            $result = [];

            /** @var FieldObject $field */
            foreach ($this->fields->getFields() as $field) {
                $fieldTitle = $field->getTitle();
                if ($fieldTitle !== $field->getDefaultTitle() && $field->isDefault())
                {
                    $result[] = $this->fieldRender->fieldToTcaColumnsOverrides($field);
                }
            }

            return implode("\n" . $this->fields->getSpacesInTcaColumnsOverrides(), $result);
        }
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function fieldsToColumn()
    {
        if ($this->fields) {
            $result = [];

            /** @var $field FieldObject  */
            foreach ($this->fields->getFields() as $field) {
                if ($field->exist()) {
                    if (!$field->isDefault()) {
                        $result[] = $this->fieldRender->fieldToTca($field);
                    }
                } else {
                    throw new InvalidArgumentException('Field "' . $field->getType() . '" does not exist.4');
                }
            }
            return implode("\n" . $this->fields->getSpacesInTcaColumn(), $result);
        }
    }

    /**
     * @return string
     */
    public function fieldsToType()
    {
        if ($this->fields) {
            $createdFields = [];

            /** @var FieldObject $field */
            foreach ($this->fields->getFields() as $field) {
                $fieldType = $field->getType();

                if ($field->isDefault()) {
                    $createdFields[] = $fieldType;
                } elseif (!$field->isDefault()) {
                    $createdFields[] = $this->fieldRender->fieldNameInTca($field);
                } else {
                    throw new InvalidArgumentException('Field "' . $fieldType . '" does not exist.5');
                }
            }

            return implode(', ', $createdFields) . ',';
        }
    }

    /**
     * @return string
     */
    public function fieldsToClassMapping()
    {
        if ($this->fields) {
            $createdFields = [];

            /** @var FieldObject $field */
            foreach ($this->fields->getFields() as $field) {
                if ($field->exist() && $field->getType() !== $field->getName()) {
                    $createdFields[] = '"' . $this->fieldRender->fieldNameInTca($field) . '" => "' . $this->fieldRender->fieldNameInModel($field) . '"';
                } elseif (!$field->exist()) {
                    throw new InvalidArgumentException('Field "' . $field->getType() . '" does not exist.6');
                }
            }

            return implode(",\n" . '        ', $createdFields);
        }
    }

    /**
     * @return string
     */
    public function fieldsToSqlTable()
    {
        if ($this->fields) {
            $result = [];

            /** @var FieldObject $field */
            foreach ($this->fields->getFields() as $field) {
                $fieldType = $field->getType();

                if ($field->exist()) {
                    if ($field->hasSqlDataType()) {
                        $result[] = $this->fieldRender->fieldNameInTca($field) . ' ' . $field->getSqlDataType();
                    }
                } else {
                    throw new InvalidArgumentException('Field "' . $fieldType . '" does not exist.3');
                }
            }

            return implode(",\n    ", $result);
        }
    }

    /**
     * @return string
     */
    public function fieldsToTypoScriptMapping()
    {
        if ($this->fields) {
            $createdFields = [];

            /** @var FieldObject $field */
            foreach ($this->fields->getFields() as $field) {
                $fieldType = $field->getType();

                if ($field->exist() && $field->getType() !== $field->getName()) {
                    $createdFields[] = $this->fieldRender->fieldNameInTca($field) . '.mapOnProperty = ' . $this->fieldRender->fieldNameInModel($field);
                } elseif (!$field->exist()) {
                    throw new InvalidArgumentException('Field "' . $fieldType . '" does not exist.2');
                }
            }

            return  implode(
                "\n" . $this->fields->getSpacesInTypoScriptMapping(),
                $createdFields
            );
        }
    }
}
