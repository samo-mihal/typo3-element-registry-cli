<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\ImportedClassesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\Model\FieldDataDescriptionRender;
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
     * Model constructor.
     * @param RenderCreateCommand $render
     */
    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
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
        $importClass = GeneralUtility::makeInstance(ImportedClassesConfig::class)->getClasses();

        $optionalClass = $this->render->getOptionalClass();
        $fields = $this->render->getFields();
        if ($fields) {
            foreach ($fields->getFields() as $field) {
                $fieldName = $field->getName();

                if ($optionalClass !== null && in_array($importClass[$optionalClass], $result) === false) {
                    $result[] = $importClass[$optionalClass];
                }
                if ($field->needImportClass()) {
                    if ($field->needImportedClassDefaultName()) {
                        if ($field->getDefaultName() === $fieldName) {
                            foreach ($field->getImportClasses() as $importClassFromField) {
                                if (in_array($importClass[$importClassFromField], $result) === false){
                                    $result[] = $importClass[$importClassFromField];
                                }
                            }
                        }
                    } else {
                        foreach ($field->getImportClasses() as $importClassFromField) {
                            if (in_array($importClass[$importClassFromField], $result) === false){
                                $result[] = $importClass[$importClassFromField];
                            }
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
     * @return string
     * Return content element model's protected and getters (string format)
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

                if ($field->getDefaultName() === $fieldName && !empty($field->getTrait()))
                {
                    if (in_array('use ' . ucfirst($field->getTrait()) . ';', $resultOfTraits) === false) {
                        $resultOfTraits[] = 'use ' . ucfirst($field->getTrait()) . ';';
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
    public function contentElementAndInlinetemplate()
    {
        $template[] = '<?php';
        $template[] = 'declare(strict_types=1);';
        $template[] = 'namespace ' . $this->render->getModelNamespace() . ';';
        $template[] = '';
        $template[] =  $this->importModelClasses();
        $template[] = 'use ' . $this->render->getContentElementAndInlineModelExtendClass() . ';';
        $template[] = '';
        $template[] = '/**';
        $template[] = ' * Class ' . $this->render->getName();
        $template[] = ' * @package ' . $this->render->getModelNamespace();
        $template[] = ' */';
        $template[] = 'class ' . $this->render->getName() . ' extends ' . end(explode('\\', $this->render->getContentElementAndInlineModelExtendClass()));
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
