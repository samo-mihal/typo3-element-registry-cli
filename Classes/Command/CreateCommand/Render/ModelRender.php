<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\ImportedClassesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\Fields\FieldDataDescriptionRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use InvalidArgumentException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
     * @var ImportedClassesConfig
     */
    protected $importedClasses = null;

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
    }

    /**
     * @param FieldObject $field
     * @return FieldObject
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function fillFieldDescription(FieldObject $field): FieldObject
    {
        return GeneralUtility::makeInstance(FieldDataDescriptionRender::class, $this->render)->getDescription($field);
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function importModelClasses()
    {
        $result = [];

        $optionalClass = $this->render->getOptionalClass();
        $fields = $this->render->getFields();
        if ($fields) {
            /** @var FieldObject $field */
            foreach ($fields->getFields() as $field) {
                $fieldName = $field->getName();
                $trait = $fieldName . 'Trait';
                if ($optionalClass !== null && in_array($this->importedClasses[$optionalClass], $result) === false) {
                    $result[] = $this->importedClasses[$optionalClass];
                }
                if ($this->importedClasses[$trait]) {
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

            return implode("\n", $result);
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
            return implode("\n    ", $result);
        }
    }

    /**
     * @return string|null
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function fields()
    {
        $fields = $this->render->getFields();

        if (!empty($fields)) {
            $betweenProtectedsAndGetters = $this->render->getBetweenProtectedsAndGetters();
            $resultOfTraits = [];
            $resultOfProtected = [];
            $resultOfGetters = [];

            /** @var FieldObject $field */
            foreach ($fields->getFields() as $field) {
                $fieldName = $field->getName();
                $trait = $fieldName . 'Trait';

                if ($this->importedClasses[$trait])
                {
                    if (in_array('use ' . ucfirst($trait) . ';', $resultOfTraits) === false) {
                        $resultOfTraits[] = 'use ' . ucfirst($trait) . ';';
                    }
                } else {
                    $field = $this->fillFieldDescription($field);

                    $resultOfProtected[] = '/**
     * @var ' . $field->getModelDataTypes()->getPropertyDataTypeDescribe() . '
     */
    protected $' . str_replace(' ','',lcfirst(ucwords(str_replace('_',' ',$fieldName)))).' = ' . $field->getModelDataTypes()->getPropertyDataType() . ';';

                    $resultOfGetters[] =
                        '/**
     * @return ' . $field->getModelDataTypes()->getGetterDataTypeDescribe() . '
     */
    public function get'.str_replace(' ','',ucwords(str_replace('_',' ',$fieldName))).'(): ' . $field->getModelDataTypes()->getGetterDataType() . '
    {
        return $this->'.str_replace(' ','',lcfirst(ucwords(str_replace('_',' ',$fieldName)))).';
    }';
                }
            }


            $resultOfTraits = implode('
    ', $resultOfTraits);

            $resultOfProtected = implode('

    ', $resultOfProtected);

            $resultOfGetters = implode('

    ', $resultOfGetters);

            $resultOfTraits = $resultOfTraits ?  $resultOfTraits . '

    ' : '';

            $resultOfProtected = $resultOfProtected ?  $resultOfProtected . '

    ' : '';

            $betweenProtectedsAndGetters = $betweenProtectedsAndGetters ?  $betweenProtectedsAndGetters . '

    ' : '';

            $resultOfGetters = $resultOfGetters ?  $resultOfGetters . '

    ' : '';

            return rtrim($resultOfTraits . $resultOfProtected . $betweenProtectedsAndGetters . $resultOfGetters);
        } else {
            return null;
        }
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function contentElementTemplate()
    {
        $template[] = '<?php';
        $template[] = 'declare(strict_types=1);';
        $template[] = 'namespace ' . $this->render->getModelNamespace() . ';';
        $template[] = '';
        $template[] =  $this->importModelClasses();
        $template[] = 'use ' . $this->render->getContentElementModelExtendClass() . ';';
        $template[] = '';
        $template[] = '/**';
        $template[] = ' * Class ' . $this->render->getName();
        $template[] = ' * @package ' . $this->render->getModelNamespace();
        $template[] = ' */';
        $template[] = 'class ' . $this->render->getName() . ' extends ' . end(explode('\\', $this->render->getContentElementModelExtendClass()));
        $template[] = '{';
        if ($this->constants()) {
            $template[] = '    ' . $this->constants();
        }

        $fields = $this->fields();
        if ($fields) {
            $template[] = '';
            $template[] = '    ' . $fields;
        }
        $template[] = '}';

        file_put_contents(
            'public/typo3conf/ext/' . $this->render->getInlineRelativePath() . '/' . $this->render->getName() . '.php',
            implode("\n", $template)
        );
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function inlineTemplate()
    {
        $template[] = '<?php';
        $template[] = 'declare(strict_types=1);';
        $template[] = 'namespace ' . $this->render->getModelNamespace() . ';';
        $template[] = '';
        $template[] =  $this->importModelClasses();
        $template[] = 'use ' . $this->render->getInlineModelExtendClass() . ';';
        $template[] = '';
        $template[] = '/**';
        $template[] = ' * Class ' . $this->render->getName();
        $template[] = ' * @package ' . $this->render->getModelNamespace();
        $template[] = ' */';
        $template[] = 'class ' . $this->render->getName() . ' extends ' . end(explode('\\', $this->render->getInlineModelExtendClass()));
        $template[] = '{';
        if ($this->constants()) {
            $template[] = '    ' . $this->constants();
        }

        $fields = $this->fields();
        if ($fields) {
            $template[] = '';
            $template[] = '    ' . $fields;
        }
        $template[] = '}';

        if (!file_exists('public/typo3conf/ext/' . $this->render->getInlineRelativePath())) {
            mkdir('public/typo3conf/ext/' . $this->render->getInlineRelativePath(), 0777, true);
        }

        file_put_contents(
            'public/typo3conf/ext/' . $this->render->getInlineRelativePath() . '/' . $this->render->getName() . '.php',
            implode("\n", $template)
        );
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function recordTemplate()
    {
        $template[] = '<?php';
        $template[] = 'declare(strict_types=1);';
        $template[] = 'namespace ' . $this->render->getModelNamespace() . ';';
        $template[] = '';
        $template[] =  $this->importModelClasses();
        $template[] = 'use ' . $this->render->getRecordModelExtendClass() . ';';
        $template[] = '';
        $template[] = '/**';
        $template[] = ' * Class ' . $this->render->getName();
        $template[] = ' * @package ' . $this->render->getModelNamespace();
        $template[] = ' */';
        $template[] = 'class ' . $this->render->getName() . ' extends ' . end(explode('\\', $this->render->getRecordModelExtendClass()));
        $template[] = '{';
        if ($this->constants()) {
            $template[] = '    ' . $this->constants();
        }

        $fields = $this->fields();
        if ($fields) {
            $template[] = '';
            $template[] = '    ' . $fields;
        }
        $template[] = '}';

        if (!file_exists('public/typo3conf/ext/' . $this->render->getInlineRelativePath())) {
            mkdir('public/typo3conf/ext/' . $this->render->getInlineRelativePath(), 0777, true);
        }

        file_put_contents(
            'public/typo3conf/ext/' . $this->render->getInlineRelativePath() . '/' . $this->render->getName() . '.php',
            implode("\n", $template)
        );
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function pageTypeTemplate()
    {
        $template[] = '<?php';
        $template[] = 'declare(strict_types=1);';
        $template[] = 'namespace ' . $this->render->getModelNamespace() . ';';
        $template[] = '';
        $template[] = 'use ' . $this->render->getPageTypeModelExtendClass() . ';';
        $template[] =  $this->importModelClasses();
        $template[] = '/**';
        $template[] = ' * Class ' . $this->render->getName();
        $template[] = ' * @package ' . $this->render->getModelNamespace();
        $template[] = ' */';
        $template[] = 'class ' . $this->render->getName() . ' extends ' . end(explode('\\', $this->render->getPageTypeModelExtendClass()));
        $template[] = '{';
        if ($this->constants()) {
            $template[] = '    ' . $this->constants();
            $template[] = '';
        }
        $template[] = '    /**';
        $template[] = '     * @var int';
        $template[] = '     */';
        $template[] = '    protected static $doktype = ' . $this->render->getDoktype() . ';';

        $fields = $this->fields();
        if ($fields) {
            $template[] = '';
            $template[] = '    ' . $fields;
        }
        $template[] = '}';

        file_put_contents(
            'public/typo3conf/ext/' . $this->render->getInlineRelativePath() . '/' . $this->render->getName() . '.php',
            implode("\n", $template)
        );
    }
}
