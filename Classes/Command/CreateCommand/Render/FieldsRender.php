<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;
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
     * TCA constructor.
     * @param RenderCreateCommand $render
     */
    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
        $this->fieldRender = GeneralUtility::makeInstance(FieldRender::class, $render);
    }

    /**
     * @return string|null
     */
    public function fieldsToPalette()
    {
        if ($this->render->getFields()) {
            $extraSpace = '            ';
            $createdFields = [];

            /** @var FieldObject  $field */
            foreach ($this->render->getFields()->getFields() as $field) {
                if ($field->isDefault()) {
                    $createdFields[] = '--linebreak--, ' . $field->getType();
                } elseif (!$field->isDefault()) {
                    $createdFields[] = '--linebreak--, ' . $this->fieldRender->fieldNameInTca($field);
                } else {
                    throw new InvalidArgumentException('Field "' . $field->getType() . '" does not exist.1');
                }
            }
            return preg_replace('/--linebreak--, /', '', implode(",\n" . $extraSpace, $createdFields),1);
        } else {
            return null;
        }
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function fieldsToColumn()
    {
        $fields = $this->render->getFields();

        if ($fields)
        {
            $result = [];

            /** @var $field FieldObject  */
            foreach ($fields->getFields() as $field) {
                $fieldType = $field->getType();

                if ($field->exist()) {
                    if (!$field->isDefault()) {
                        $result[] = $this->fieldRender->fieldToTca($field);
                    }
                } else {
                    throw new InvalidArgumentException('Field "' . $fieldType . '" does not exist.4');
                }
            }

            return implode("\n" . $fields->getSpacesInTcaColumn(), $result);
        }
    }

    /**
     * @return string
     */
    public function fieldsToType()
    {
        $fields = $this->render->getFields();
        if ($fields) {
            $createdFields = [];

            /** @var FieldObject $field */
            foreach ($fields->getFields() as $field) {
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
    public function fieldsToSqlTable()
    {
        $fields = $this->render->getFields();

        if ($fields) {
            $result = [];

            /** @var FieldObject $field */
            foreach ($fields->getFields() as $field) {
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
}
