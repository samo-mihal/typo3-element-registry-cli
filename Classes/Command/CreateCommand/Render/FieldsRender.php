<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\ImportedClassesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\FieldsObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\Fields\FieldConfigRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\Fields\FieldRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;
use InvalidArgumentException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

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
     * @var StandaloneView
     */
    protected $view = null;

    /**
     * @var ImportedClassesConfig
     */
    protected $importedClasses = null;

    /**
     * FieldsRender constructor.
     * @param RenderCreateCommand $render
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
        $this->importedClasses = GeneralUtility::makeInstance(ImportedClassesConfig::class, $render)->getClasses();
        $this->fieldRender = GeneralUtility::makeInstance(FieldRender::class, $render);
        $this->fieldConfigRender = GeneralUtility::makeInstance(FieldConfigRender::class, $render);
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
                    $result[] = $this->fieldRender->fieldToTcaColumnsOverrides($field);
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
                        $field = $this->fieldRender->fillFieldDescription($field);

                        $protected = clone $this->view;
                        $protected->setTemplatePathAndFilename(
                            GeneralUtility::getFileAbsFileName(
                                'EXT:typo3_element_registry_cli/Resources/Private/Templates/Model/ModelProtectedTemplate.html'
                            )
                        );
                        $protected->assignMultiple([
                            'propertyDataDescribe' => $field->getModelDataTypes()->getPropertyDataTypeDescribe(),
                            'propertyDataType' => $field->getModelDataTypes()->getPropertyDataType(),
                            'fieldNameInModel' => $this->fieldRender->fieldNameInModel($field),
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
                            'fieldNameInModel' => $this->fieldRender->fieldNameInModel($field),
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
}
