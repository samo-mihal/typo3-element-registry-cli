<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\ImportedClassesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\Fields\FieldRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;
use InvalidArgumentException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FieldsRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
class FieldsRender extends AbstractRender
{
    /**
     * @var ImportedClassesConfig
     */
    protected $importedClasses = null;

    /**
     * FieldsRender constructor.
     * @param ElementRender $render
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function __construct(ElementRender $render)
    {
        parent::__construct($render);
        $this->importedClasses = GeneralUtility::makeInstance(ImportedClassesConfig::class, $render)->getClasses();
    }

    /**
     * @return string|null
     */
    public function fieldsToPalette()
    {
        if ($this->fields) {
            $createdFields = [];

            /** @var FieldObject  $field */
            foreach ($this->fields as $field) {
                if ($field->exist()) {
                    $createdFields[] = '--linebreak--, ' . $field->getNameInTCA($this->elementRender->getElement());
                } else {
                    throw new InvalidArgumentException('Field "' . $field->getType() . '" does not exist.1');
                }
            }
            return preg_replace('/--linebreak--, /', '',
                implode(
                    ",\n" . $this->elementRender->getElement()->getFieldsSpacesInTcaPalette(),
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
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function fieldsToColumnsOverrides()
    {
        if ($this->fields) {
            $result = [];

            /** @var FieldObject $field */
            foreach ($this->fields as $field) {
                $fieldTitle = $field->getTitle();
                if ($fieldTitle !== $field->getDefaultTitle() && $field->isDefault())
                {
                    $result[] = $this->getFieldRender($field)->fieldToTcaColumnsOverrides();
                }
            }

            return implode("\n" . $this->elementRender->getElement()->getFieldsSpacesInTcaColumnsOverrides(), $result);
        }
    }

    /**
     * @return string
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function fieldsToColumn()
    {
        if ($this->fields) {
            $result = [];

            /** @var $field FieldObject  */
            foreach ($this->fields as $field) {
                if ($field->exist()) {
                    if (!$field->isDefault()) {
                        $result[] = $this->getFieldRender($field)->fieldToTca();
                    }
                } else {
                    throw new InvalidArgumentException('Field "' . $field->getType() . '" does not exist.4');
                }
            }
            return implode("\n" . $this->elementRender->getElement()->getFieldsSpacesInTcaColumn(), $result);
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
            foreach ($this->fields as $field) {
                $fieldType = $field->getType();

                if ($field->isDefault()) {
                    $createdFields[] = $fieldType;
                } elseif (!$field->isDefault()) {
                    $createdFields[] = $field->getNameInTCA($this->elementRender->getElement());
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
            foreach ($this->fields as $field) {
                if ($field->exist() && $field->getType() !== $field->getName()) {
                    $createdFields[] = '"' . $field->getNameInTCA($this->elementRender->getElement()) . '" => "' . $field->getNameInModel() . '"';
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
            foreach ($this->fields as $field) {
                $fieldType = $field->getType();

                if ($field->exist()) {
                    if ($field->hasSqlDataType()) {
                        $result[] = $field->getNameInTCA($this->elementRender->getElement()) . ' ' . $field->getSqlDataType();
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
            foreach ($this->fields as $field) {
                $fieldType = $field->getType();

                if ($field->exist() && $field->getType() !== $field->getNameInTCA($this->element)) {
                    $createdFields[] = $field->getNameInTCA($this->elementRender->getElement()) . '.mapOnProperty = ' . $field->getNameInModel();
                } elseif (!$field->exist()) {
                    throw new InvalidArgumentException('Field "' . $fieldType . '" does not exist.2');
                }
            }

            return  implode(
                "\n" . $this->elementRender->getElement()->getFieldsSpacesInTypoScriptMapping(),
                $createdFields
            );
        }
    }

    /**
     * @param $filename
     * @return string|null
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function fieldsToModel($filename)
    {
        if ($this->fields) {
            $betweenProtectedsAndGetters = $this->elementRender->getElement()->getBetweenProtectedsAndGetters();
            $resultOfTraits = [];
            $resultOfProtected = [];
            $resultOfGetters = [];

            /** @var FieldObject $field */
            foreach ($this->fields as $field) {
                if ($field->hasModel()) {
                    $fieldName = $field->getName();
                    $trait = $fieldName . 'Trait';

                    if ($this->importedClasses[$trait] && strpos($this->importedClasses[$trait], $this->elementRender->getElement()->getType()) !== false)
                    {
                        if (in_array('use ' . ucfirst($trait) . ';', $resultOfTraits) === false) {
                            $resultOfTraits[] = '    use ' . ucfirst($trait) . ';';
                        }
                    } else {
                        $field = $this->getFieldRender($field)->fillFieldDescription();

                        $protected = clone $this->view;
                        $protected->setTemplatePathAndFilename(
                            GeneralUtility::getFileAbsFileName(
                                'EXT:typo3_element_registry_cli/Resources/Private/Templates/Model/ModelProtectedTemplate.html'
                            )
                        );
                        $protected->assignMultiple([
                            'propertyDataDescribe' => $field->getModelDataTypes()->getPropertyDataTypeDescribe(),
                            'propertyDataType' => $field->getModelDataTypes()->getPropertyDataType(),
                            'fieldNameInModel' => $field->getNameInModel(),
                        ]);
                        $resultOfProtected[] = $protected->render();

                        $getter = clone $this->view;
                        $getter->setTemplatePathAndFilename(
                            GeneralUtility::getFileAbsFileName(
                                'EXT:typo3_element_registry_cli/Resources/Private/Templates/Model/ModelGetterTemplate.html'
                            )
                        );
                        $getter->assignMultiple([
                            'getterDataDescribe' => $field->getModelDataTypes()->getGetterDataTypeDescribe(),
                            'getterDataType' => $field->getModelDataTypes()->getGetterDataType(),
                            'fieldNameInModel' => $field->getNameInModel(),
                        ]);
                        $resultOfGetters[] = $getter->render();
                    }
                }
            }

            $resultOfTraits = $resultOfTraits ? implode("\n", $resultOfTraits) . "\n\n" : '';
            $resultOfProtected = $resultOfProtected ? implode("\n", $resultOfProtected). "\n" : '';
            $betweenProtectedsAndGetters = $betweenProtectedsAndGetters ?  $betweenProtectedsAndGetters . "\n" : '';
            $resultOfGetters = $resultOfGetters ? implode("\n", $resultOfGetters). "\n" : '';



            GeneralCreateCommandUtility::importStringInToFileAfterString(
                $filename,
                [rtrim($resultOfTraits . $resultOfProtected . $betweenProtectedsAndGetters . $resultOfGetters) . "\n"],
                '{',
                0
            );
        }
    }

    /**
     * @param $field
     * @return FieldRender|object
     */
    public function getFieldRender($field)
    {
        return GeneralUtility::makeInstance(FieldRender::class, $this->elementRender, $field);
    }
}
