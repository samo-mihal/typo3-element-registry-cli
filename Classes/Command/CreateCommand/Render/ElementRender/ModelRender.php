<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\ImportedClassesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\FieldObject;
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
     * @param ElementRender $elementRender
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function __construct(ElementRender $elementRender)
    {
        parent::__construct($elementRender);
        $this->importedClasses = GeneralUtility::makeInstance(ImportedClassesConfig::class, $elementRender)->getClasses();
        $this->view->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName(
                'EXT:typo3_element_registry_cli/Resources/Private/Templates/Model/ModelTemplate.html'
            )
        );
        $this->fieldsRender = GeneralUtility::makeInstance(FieldsRender::class, $elementRender);
        $this->filename = $this->elementRender->getElement()->getModelPath() . '/' . $this->elementRender->getElement()->getName() . '.php';
    }

    public function importModelClasses()
    {
        $fields = $this->fields;
        if ($fields) {
            $result = [];
            $optionalClass = $this->elementRender->getElement()->getOptionalClass();
            /** @var FieldObject $field */
            foreach ($fields as $field) {
                $fieldName = $field->getName();
                $trait = $fieldName . 'Trait';
                if ($optionalClass !== null && in_array($this->importedClasses[$optionalClass], $result) === false) {
                    $result[] = $this->importedClasses[$optionalClass];
                }
                if ($this->importedClasses[$trait] && strpos($this->importedClasses[$trait], $this->elementRender->getElement()->getType()) !== false) {
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
        $fields = $this->fields;
        if ($fields) {
            $result = [];

            /** @var FieldObject $field */
            foreach ($fields as $field) {
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
        if (!file_exists($this->filename) && $this->fields) {
            $this->view->assignMultiple([
                'modelNamespace' => $this->elementRender->getElement()->getModelNamespace(),
                'name' => $this->elementRender->getElement()->getName(),
                'modelExtendClass' => $this->elementRender->getElement()->getContentElementModelExtendClass(),
                'modelExtendClassEnd' => end(explode('\\', $this->elementRender->getElement()->getContentElementModelExtendClass()))
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
        if (!file_exists($this->filename) && $this->fields) {
            if (!file_exists($this->elementRender->getElement()->getModelPath())) {
                mkdir($this->elementRender->getElement()->getModelPath(), 0777, true);
            }

            $this->view->assignMultiple([
                'modelNamespace' => $this->elementRender->getElement()->getModelNamespace(),
                'name' => $this->elementRender->getElement()->getName(),
                'modelExtendClass' => $this->elementRender->getElement()->getInlineModelExtendClass(),
                'modelExtendClassEnd' => end(explode('\\', $this->elementRender->getElement()->getInlineModelExtendClass()))
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
        if (!file_exists($this->filename) && $this->fields) {
            if (!file_exists($this->elementRender->getElement()->getModelPath())) {
                mkdir($this->elementRender->getElement()->getModelPath(), 0777, true);
            }

            $this->view->assignMultiple([
                'modelNamespace' => $this->elementRender->getElement()->getModelNamespace(),
                'name' => $this->elementRender->getElement()->getName(),
                'modelExtendClass' => $this->elementRender->getElement()->getRecordModelExtendClass(),
                'modelExtendClassEnd' => end(explode('\\', $this->elementRender->getElement()->getRecordModelExtendClass()))
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
        if (!file_exists($this->filename)) {
            $this->view->assignMultiple([
                'modelNamespace' => $this->elementRender->getElement()->getModelNamespace(),
                'name' => $this->elementRender->getElement()->getName(),
                'modelExtendClass' => $this->elementRender->getElement()->getPageTypeModelExtendClass(),
                'modelExtendClassEnd' => end(explode('\\', $this->elementRender->getElement()->getPageTypeModelExtendClass()))
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
