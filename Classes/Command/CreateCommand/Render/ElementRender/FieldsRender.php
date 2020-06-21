<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\PHPClassBuilder\Object\PHPClassObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\FlexFormFieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\ImportedClassesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\Fields\FieldRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\ElementSetup;
use DOMDocument;
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
            return preg_replace(
                '/--linebreak--, /',
                '',
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
                if (($fieldTitle !== $field->getDefaultTitle() || $field->hasItems()) && $field->isDefault()) {
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

                if ($field->getName() !== $field->getNameInTCA($this->element)) {
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
     * @param PHPClassObject $modelClass
     * @return void
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function fieldsToModel(PHPClassObject $modelClass): void
    {
        if ($this->fields) {
            if (
                $this->element->getStaticType() === ElementSetup::PAGE_TYPE &&
                $modelClass->contains()->variable('doktype') === false
            ) {
                $modelClass->addVariable()
                    ->setName('doktype')
                    ->setType('protected static')
                    ->setComment('/** @var int  */')
                    ->setValue((int)$this->element->getDoktype());
            }
            /** @var FieldObject $field */
            foreach ($this->fields as $field) {
                if ($field->hasModel()) {
                    $trait = $field->getName() . 'Trait';
                    if (
                        $this->importedClasses[$trait] &&
                        strpos(
                            $this->importedClasses[$trait],
                            str_replace(
                                ' ',
                                '',
                                ucwords($this->element->getStaticType())
                            )
                        ) !== false
                    ) {
                        if ($modelClass->contains()->trait(ucfirst($trait)) === false) {
                            $modelClass->addTrait()
                                ->setName(ucfirst($trait));
                        }
                    } else {
                        $field = $this->getFieldRender($field)->fillFieldDescription();

                        $modelClass->addVariable()
                            ->setName($field->getNameInModel())
                            ->setType('protected')
                            ->setValue($field->getModelDataTypes()->getPropertyDataType())
                            ->setComment('/** @var ' . $field->getModelDataTypes()->getPropertyDataTypeDescribe() . '  */');

                        $functionContent = '{' . "\n" .
                            $modelClass->getTabSpaces() . $modelClass->getTabSpaces() .
                            'return $this->' . $field->getNameInModel() . ';' . "\n" .
                            $modelClass->getTabSpaces() . '}';


                        $modelClass->addFunction()
                            ->setName('get' . ucfirst($field->getNameInModel()))
                            ->setType('public function')
                            ->setContent($functionContent)
                            ->setArgumentsAndDescription('(): ' . $field->getModelDataTypes()->getGetterDataType())
                            ->setComment('/** @return ' . $field->getModelDataTypes()->getGetterDataTypeDescribe() . ' */');
                    }
                }
            }
        }
    }

    /**
     * @param $fields
     * @return void
     */
    public function fieldsToFlexForm($fields): void
    {
        $xml = simplexml_load_file($this->element->getFlexFormPath());

        $xml->sheets->General->ROOT;
        $element = $xml->sheets->General->ROOT->el;
        $name = $this->elementRender->getElement()->getName();
        $flexFormFieldTypes = GeneralUtility::makeInstance(FlexFormFieldTypesConfig::class)->getFlexFormFieldTypes();

        /** @var FieldObject $field */
        foreach ($fields as $field) {
            $fieldName = $field->getName();
            $fieldType = $field->getType();
            $fieldTitle = $field->getTitle();

            if ($flexFormFieldTypes[$fieldType]) {
                $field = $element->addChild($fieldName);
                $TCEForms = $field->addChild('TCEforms');
                $TCEForms->addChild(
                    'label',
                    $this->element->getTranslationPathShort() . ':' . $name . 'FlexForm.General' . $fieldName
                );
                $TCEForms->addChild('config', $flexFormFieldTypes[$fieldType]['config']);

                $this->elementRender->translation()->addStringToTranslation(
                    lcfirst($name) . ".FlexForm.General.". $fieldName,
                    $fieldTitle
                );
            }
        }

        /** Save FlexForm */
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML(
            str_replace(['&lt;', '&gt;'], ['<', '>'], $xml->asXML())
        );
        $dom->save($this->element->getFlexFormPath());
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
