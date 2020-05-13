<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\ImportedClassesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\FieldsObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\Fields\FieldRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;
use InvalidArgumentException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class FieldsRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
class FieldsRender
{
    /**
     * @var null
     */
    protected $render = null;

    /**
     * @var FieldsObject
     */
    protected $fields = null;

    /**
     * @var StandaloneView
     */
    protected $view = null;

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
        $this->render = $render;
        $this->importedClasses = GeneralUtility::makeInstance(ImportedClassesConfig::class, $render)->getClasses();
        $this->fields = $this->render->getFields();
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
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
                    $createdFields[] = '--linebreak--, ' . $field->getNameInTCA($this->render);
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
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
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
                    $result[] = $this->getFieldRender($field)->fieldToTcaColumnsOverrides($field);
                }
            }

            return implode("\n" . $this->fields->getSpacesInTcaColumnsOverrides(), $result);
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
            foreach ($this->fields->getFields() as $field) {
                if ($field->exist()) {
                    if (!$field->isDefault()) {
                        $result[] = $this->getFieldRender($field)->fieldToTca();
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
                    $createdFields[] = $field->getNameInTCA($this->render);
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
                    $createdFields[] = '"' . $field->getNameInTCA($this->render) . '" => "' . $field->getNameInModel() . '"';
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
                        $result[] = $field->getNameInTCA($this->render) . ' ' . $field->getSqlDataType();
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
                    $createdFields[] = $field->getNameInTCA($this->render) . '.mapOnProperty = ' . $field->getNameInModel();
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

    /**
     * @param $filename
     * @return string|null
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function fieldsToModel($filename)
    {
        if ($this->fields) {
            $betweenProtectedsAndGetters = $this->render->getBetweenProtectedsAndGetters();
            $resultOfTraits = [];
            $resultOfProtected = [];
            $resultOfGetters = [];

            /** @var FieldObject $field */
            foreach ($this->fields->getFields() as $field) {
                if ($field->hasModel()) {
                    $fieldName = $field->getName();
                    $trait = $fieldName . 'Trait';

                    if ($this->importedClasses[$trait] && strpos($this->importedClasses[$trait], $this->render->getElementType()) !== false)
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
        return GeneralUtility::makeInstance(FieldRender::class, $this->render, $field);
    }
}
