<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\PHPClassBuilder\Object\PHPClassObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\ImportedClassesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use InvalidArgumentException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ModelRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
class ModelRender extends AbstractRender
{
    /**
     * @var string
     */
    protected $filename = '';

    /**
     * @var ImportedClassesConfig
     */
    protected $importedClasses = null;

    /**
     * @var FieldsRender
     */
    protected $fieldsRender = null;

    /**
     * @var PHPClassObject
     */
    protected $modelClass = null;

    /**
     * ModelRender constructor.
     * @param ElementRender $elementRender
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function __construct(ElementRender $elementRender)
    {
        parent::__construct($elementRender);
        $this->importedClasses = GeneralUtility::makeInstance(ImportedClassesConfig::class, $elementRender)->getClasses();
        $this->fieldsRender = GeneralUtility::makeInstance(FieldsRender::class, $elementRender);
        $this->filename = $this->elementRender->getElement()->getModelDirPath() . '/' . $this->elementRender->getElement()->getName() . '.php';

        $this->modelClass = new PHPClassObject($this->filename);
        $this->modelClass->setStrictMode(true);
        $this->modelClass->setName($this->element->getName());
        $this->modelClass->setNameSpace($this->element->getModelNamespace());
        $this->modelClass->setComment(
            '/**
 * Class ' . $this->element->getName() . '
 * @package ' . $this->element->getModelNamespace() . '
 */'
        );
    }

    /**
     * ModelRender destructor.
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function __destruct()
    {
        $this->importModelClasses();
        $this->fieldsRender->fieldsToModel($this->modelClass);
        $this->constants();
        $this->modelClass->render();
    }

    /**
     * @return void
     */
    public function importModelClasses(): void
    {
        $fields = $this->fields;
        if ($fields) {
            /** @var FieldObject $field */
            foreach ($fields as $field) {
                $fieldName = $field->getName();
                $trait = $fieldName . 'Trait';
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
                    if ($this->modelClass->contains()->usedClass($this->importedClasses[$trait]) === false) {
                        $this->modelClass->addUsedClass()
                            ->setName($this->importedClasses[$trait]);
                    }
                }
                if ($field->getImportClasses()) {
                    foreach ($field->getImportClasses() as $importClassFromField) {
                        if ($this->modelClass->contains()->usedClass($this->importedClasses[$importClassFromField]) === false) {
                            $this->modelClass->addUsedClass()
                                ->setName($this->importedClasses[$importClassFromField]);
                        }
                    }
                }
            }
        }
    }

    /**
     * @return void
     */
    public function constants(): void
    {
        $fields = $this->fields;
        if ($fields) {
            /** @var FieldObject $field */
            foreach ($fields as $field) {
                $fieldName = $field->getName();
                $fieldType = $field->getType();
                $fieldItems = $field->getItems();
                if ($field->isTCAItemsAllowed()) {
                    foreach ($fieldItems as $item) {
                        $itemName = $item->getName();
                        $itemValue = $item->getValue();
                        $this->modelClass->addConstant()
                            ->setName(strtoupper($fieldName) . '_' .strtoupper($itemName))
                            ->setValue("'" . $itemValue ."'");
                    }
                } elseif (!empty($fieldItems) && !$field->isFlexFormItemsAllowed() && !$field->isInlineItemsAllowed()) {
                    throw new InvalidArgumentException('You can not add items to ' . $fieldType . ', because items is not allowed.1');
                }
            }
        }
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function contentElementTemplate()
    {
        $this->modelClass->setExtendsOrImplements(
            'extends \\' . $this->element->getContentElementModelExtendClass()
        );
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function inlineTemplate()
    {
        $this->modelClass->setExtendsOrImplements(
            'extends \\' . $this->element->getInlineModelExtendClass()
        );
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function recordTemplate()
    {
        $this->modelClass->setExtendsOrImplements(
            'extends \\' . $this->element->getRecordModelExtendClass()
        );
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function pageTypeTemplate()
    {
        $this->modelClass->setExtendsOrImplements(
            'extends \\' . $this->element->getPageTypeModelExtendClass()
        );
    }
}
