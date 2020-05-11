<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\ImportedClassesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\Fields\FieldDataDescriptionRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;
use InvalidArgumentException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class Model
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render
 */
class ModelRender
{
    /**
     * @var null
     */
    protected $render = null;

    /**
     * @var string
     */
    protected $filename = '';

    /**
     * @var ImportedClassesConfig
     */
    protected $importedClasses = null;

    /**
     * @var StandaloneView
     */
    protected $view = null;

    /**
     * @var FieldsRender
     */
    protected $fieldsRender = null;

    /**
     * ModelRender constructor.
     * @param RenderCreateCommand $render
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
        $this->importedClasses = GeneralUtility::makeInstance(ImportedClassesConfig::class, $render)->getClasses();
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
        $this->view->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName(
                'EXT:typo3_element_registry_cli/Resources/Private/Templates/Model/ModelTemplate.html'
            )
        );
        $this->fieldsRender = GeneralUtility::makeInstance(FieldsRender::class, $render);
        $this->filename = 'public/typo3conf/ext/' . $this->render->getInlineRelativePath() . '/' . $this->render->getName() . '.php';
    }

    public function importModelClasses()
    {
        $fields = $this->render->getFields();
        if ($fields) {
            $result = [];
            $optionalClass = $this->render->getOptionalClass();
            /** @var FieldObject $field */
            foreach ($fields->getFields() as $field) {
                $fieldName = $field->getName();
                $trait = $fieldName . 'Trait';
                if ($optionalClass !== null && in_array($this->importedClasses[$optionalClass], $result) === false) {
                    $result[] = $this->importedClasses[$optionalClass];
                }
                if ($this->importedClasses[$trait] && strpos($this->importedClasses[$trait], $this->render->getElementType()) !== false) {
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
        $fields = $this->render->getFields();
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
        if (!file_exists($this->filename) && $this->render->getFields()) {
            $this->view->assignMultiple([
                'modelNamespace' => $this->render->getModelNamespace(),
                'name' => $this->render->getName(),
                'modelExtendClass' => $this->render->getContentElementModelExtendClass(),
                'modelExtendClassEnd' => end(explode('\\', $this->render->getContentElementModelExtendClass()))
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
        if (!file_exists($this->filename) && $this->render->getFields()) {
            if (!file_exists('public/typo3conf/ext/' . $this->render->getInlineRelativePath())) {
                mkdir('public/typo3conf/ext/' . $this->render->getInlineRelativePath(), 0777, true);
            }

            $this->view->assignMultiple([
                'modelNamespace' => $this->render->getModelNamespace(),
                'name' => $this->render->getName(),
                'modelExtendClass' => $this->render->getInlineModelExtendClass(),
                'modelExtendClassEnd' => end(explode('\\', $this->render->getInlineModelExtendClass()))
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
        if (!file_exists($this->filename) && $this->render->getFields()) {
            if (!file_exists('public/typo3conf/ext/' . $this->render->getInlineRelativePath())) {
                mkdir('public/typo3conf/ext/' . $this->render->getInlineRelativePath(), 0777, true);
            }

            $this->view->assignMultiple([
                'modelNamespace' => $this->render->getModelNamespace(),
                'name' => $this->render->getName(),
                'modelExtendClass' => $this->render->getRecordModelExtendClass(),
                'modelExtendClassEnd' => end(explode('\\', $this->render->getRecordModelExtendClass()))
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
        if (!file_exists($this->filename) && $this->render->getFields()) {
            $this->view->assignMultiple([
                'modelNamespace' => $this->render->getModelNamespace(),
                'name' => $this->render->getName(),
                'modelExtendClass' => $this->render->getPageTypeModelExtendClass(),
                'modelExtendClassEnd' => end(explode('\\', $this->render->getPageTypeModelExtendClass()))
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
