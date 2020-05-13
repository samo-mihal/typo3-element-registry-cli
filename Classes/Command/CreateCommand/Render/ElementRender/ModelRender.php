<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\ImportedClassesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;
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
     * ModelRender constructor.
     * @param ElementRender $element
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function __construct(ElementRender $element)
    {
        parent::__construct($element);
        $this->importedClasses = GeneralUtility::makeInstance(ImportedClassesConfig::class, $element)->getClasses();
        $this->view->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName(
                'EXT:typo3_element_registry_cli/Resources/Private/Templates/Model/ModelTemplate.html'
            )
        );
        $this->fieldsRender = GeneralUtility::makeInstance(FieldsRender::class, $element);
        $this->filename = 'public/typo3conf/ext/' . $this->element->getInlineRelativePath() . '/' . $this->element->getName() . '.php';
    }

    public function importModelClasses()
    {
        $fields = $this->element->getFields();
        if ($fields) {
            $result = [];
            $optionalClass = $this->element->getOptionalClass();
            /** @var FieldObject $field */
            foreach ($fields->getFields() as $field) {
                $fieldName = $field->getName();
                $trait = $fieldName . 'Trait';
                if ($optionalClass !== null && in_array($this->importedClasses[$optionalClass], $result) === false) {
                    $result[] = $this->importedClasses[$optionalClass];
                }
                if ($this->importedClasses[$trait] && strpos($this->importedClasses[$trait], $this->element->getElementType()) !== false) {
                    if (in_array($this->importedClasses[$trait], $result) === false){
                        $result[] = $this->importedClasses[$trait];
                    }
                }
                if ($field->getImportClasses()) {
                    foreach ($field->getImportClasses() as $importClassFromField) {
                        if (in_array($this->importedClasses[$importClassFromField], $result) === false){
                            $result[] = $this->importedClasses[$importClassFromField];
                        }
                    }
                }
            }

            if ($result) {
                GeneralCreateCommandUtility::importStringInToFileAfterString(
                    $this->filename,
                    [implode("\n", $result) . "\n"],
                    'declare(strict_types=1);',
                    2
                );
            }
        }
    }

    /**
     * @return string
     */
    public function constants()
    {
        $fields = $this->element->getFields();
        if ($fields) {
            $result = [];

            /** @var FieldObject $field */
            foreach ($fields->getFields() as $field) {
                $fieldName = $field->getName();
                $fieldType = $field->getType();
                $fieldItems = $field->getItems();
                if ($field->isTCAItemsAllowed()) {
                    foreach ($fieldItems as $item) {
                        $itemName = $item->getName();
                        $itemValue = $item->getValue();
                        $result[] =  'const ' . strtoupper($fieldName) . '_' .strtoupper($itemName) . ' = ' . '"' . $itemValue . '";';
                    }
                } elseif (!empty($fieldItems) && !$field->isFlexFormItemsAllowed() && !$field->isInlineItemsAllowed()) {
                    throw new InvalidArgumentException('You can not add items to ' . $fieldType . ', because items is not allowed.1');
                }
            }
            if ($result) {
                GeneralCreateCommandUtility::importStringInToFileAfterString(
                    $this->filename,
                    [implode("    \n", $result) . "\n"],
                    '{',
                    0
                );
            }
        }
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function contentElementTemplate()
    {
        if (!file_exists($this->filename) && $this->element->getFields()) {
            $this->view->assignMultiple([
                'modelNamespace' => $this->element->getModelNamespace(),
                'name' => $this->element->getName(),
                'modelExtendClass' => $this->element->getContentElementModelExtendClass(),
                'modelExtendClassEnd' => end(explode('\\', $this->element->getContentElementModelExtendClass()))
            ]);
            file_put_contents(
                $this->filename,
                $this->view->render()
            );
        }
        $this->importModelClasses();
        $this->fieldsRender->fieldsToModel($this->filename);
        $this->constants();
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function inlineTemplate()
    {
        if (!file_exists($this->filename) && $this->element->getFields()) {
            if (!file_exists('public/typo3conf/ext/' . $this->element->getInlineRelativePath())) {
                mkdir('public/typo3conf/ext/' . $this->element->getInlineRelativePath(), 0777, true);
            }

            $this->view->assignMultiple([
                'modelNamespace' => $this->element->getModelNamespace(),
                'name' => $this->element->getName(),
                'modelExtendClass' => $this->element->getInlineModelExtendClass(),
                'modelExtendClassEnd' => end(explode('\\', $this->element->getInlineModelExtendClass()))
            ]);

            file_put_contents(
                $this->filename,
                $this->view->render()
            );
        }

        $this->importModelClasses();
        $this->fieldsRender->fieldsToModel($this->filename);
        $this->constants();
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function recordTemplate()
    {
        if (!file_exists($this->filename) && $this->element->getFields()) {
            if (!file_exists('public/typo3conf/ext/' . $this->element->getInlineRelativePath())) {
                mkdir('public/typo3conf/ext/' . $this->element->getInlineRelativePath(), 0777, true);
            }

            $this->view->assignMultiple([
                'modelNamespace' => $this->element->getModelNamespace(),
                'name' => $this->element->getName(),
                'modelExtendClass' => $this->element->getRecordModelExtendClass(),
                'modelExtendClassEnd' => end(explode('\\', $this->element->getRecordModelExtendClass()))
            ]);

            file_put_contents(
                $this->filename,
                $this->view->render()
            );
        }

        $this->importModelClasses();
        $this->fieldsRender->fieldsToModel($this->filename);
        $this->constants();
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function pageTypeTemplate()
    {
        if (!file_exists($this->filename) && $this->element->getFields()) {
            $this->view->assignMultiple([
                'modelNamespace' => $this->element->getModelNamespace(),
                'name' => $this->element->getName(),
                'modelExtendClass' => $this->element->getPageTypeModelExtendClass(),
                'modelExtendClassEnd' => end(explode('\\', $this->element->getPageTypeModelExtendClass()))
            ]);

            file_put_contents(
                $this->filename,
                $this->view->render()
            );
        }
        $this->importModelClasses();
        $this->fieldsRender->fieldsToModel($this->filename);
        $this->constants();
    }
}
