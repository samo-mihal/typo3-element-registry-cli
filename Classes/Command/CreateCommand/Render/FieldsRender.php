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
     * TCA constructor.
     * @param RenderCreateCommand $render
     */
    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
    }

    /**
     * @return string|null
     */
    public function fieldsToPalette()
    {
        if ($this->render->getFields()) {
            $name = $this->render->getName();
            $extraSpace = '            ';
            $createdFields = [];

            foreach ($this->render->getFields()->getFields() as $field) {
                if ($field->isDefault()) {
                    $createdFields[] = '--linebreak--, ' . $field->getType();
                } elseif (!$field->isDefault()) {
                    $createdFields[] = '--linebreak--, ' . strtolower($name) . '_' . $field->getName();
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
            $extraSpaces2 = '    ';
            $result = [];

            /** @var $field FieldObject  */
            foreach ($fields->getFields() as $field) {
                $fieldType = $field->getType();

                if ($field->exist()) {
                    if (!$field->isDefault()) {
                        $result[] = GeneralUtility::makeInstance(FieldRender::class, $this->render)->fieldToTca($field);
                    }
                } else {
                    throw new InvalidArgumentException('Field "' . $fieldType . '" does not exist.4');
                }
            }

            return implode("\n" . $extraSpaces2, $result);
        }
    }

    /**
     * @return string
     */
    public function fieldsToType()
    {
        $fields = $this->render->getFields();
        if ($fields) {
            $name = $this->render->getName();
            $createdFields = [];

            foreach ($fields->getFields() as $field) {
                $fieldName = $field->getName();
                $fieldType = $field->getType();

                if ($field->isDefault()) {
                    $createdFields[] = $fieldType;
                } elseif (!$field->isDefault()) {
                    $createdFields[] = strtolower($name).'_'.$fieldName;
                } else {
                    throw new InvalidArgumentException('Field "' . $fieldType . '" does not exist.5');
                }
            }

            return implode(', ', $createdFields) . ',';
        }
    }
}
