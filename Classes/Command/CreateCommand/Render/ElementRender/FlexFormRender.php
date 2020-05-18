<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\FlexFormFieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;
use InvalidArgumentException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class FlexFormRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
class FlexFormRender extends AbstractRender
{
    /**
     * @var ObjectStorage
     */
    protected $flexFormFields = null;

    /**
     * FlexFormRender constructor.
     * @param ElementRender $elementRender
     */
    public function __construct(ElementRender $elementRender)
    {
        parent::__construct($elementRender);
    }

    /**
     * @return ObjectStorage
     */
    public function getFlexFormFields(): ObjectStorage
    {
        return $this->flexFormFields;
    }

    /**
     * @param ObjectStorage $flexFormFields
     */
    public function setFlexFormFields(ObjectStorage $flexFormFields): void
    {
        $this->flexFormFields = $flexFormFields;
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function addFieldsToFlexForm()
    {
        $fields = $this->getFlexFormFields();
        $name = $this->elementRender->getElement()->getName();
        $extensionName = $this->elementRender->getElement()->getExtensionName();
        $flexFormFieldTypes = GeneralUtility::makeInstance(FlexFormFieldTypesConfig::class)->getFlexFormFieldTypes();
        $result = [];

        /** @var FieldObject $field */
        foreach ($fields as $field) {
            $fieldName = $field->getName();
            $fieldType = $field->getType();
            $fieldTitle = $field->getTitle();

            if ($flexFormFieldTypes[$fieldType]) {
                $result[] = "<" . $fieldName . ">
                        <TCEforms>
                            <label>LLL:EXT:" . $extensionName . "/Resources/Private/Language/locallang_db.xlf:" . strtolower($name) . ".FlexForm.General.". $fieldName . "</label>
                            <config>
                                " . $flexFormFieldTypes[$fieldType]['config'] . "
                            </config>
                        </TCEforms>
                    </" . $fieldName . ">";

                $this->elementRender->translation()->addStringToTranslation(
                    strtolower($name) . ".FlexForm.General.". $fieldName,
                    $fieldTitle
                );
            } else {
                throw new InvalidArgumentException('Field type ' . $fieldType . ' does not exist in FlexForm field types.');
            }
        }
        return '                    ' . implode("\n                    ", $result);
    }

    /**
     * @return void
     */
    public function createFlexForm(): void
    {
        $CEFlexFormContent = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<T3DataStructure>
    <meta>
        <langDisable>1</langDisable>
    </meta>
    <sheets>
        <sDEF>
            <ROOT>
                <type>array</type>
                <el>

                </el>
            </ROOT>
        </sDEF>
    </sheets>
</T3DataStructure>
';
        if (!file_exists($this->element->getFlexFormDirPath())) {
            mkdir($this->element->getFlexFormDirPath(), 0777, true);
        }
        file_put_contents($this->element->getFlexFormPath(), $CEFlexFormContent);
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function contentElementTemplate()
    {
        $fields = $this->fields;

        if ($fields) {
            /** @var FieldObject $field */
            foreach ($fields as $field) {
                if ($field->isFlexFormItemsAllowed())
                {
                    if (!file_exists($this->element->getFlexFormPath())) {
                        $this->createFlexForm();
                    }
                    $this->setFlexFormFields(
                        $this->elementRender->getElement()->getInlineFields()[$field->getFirstItem()->getType()]
                    );
                    GeneralCreateCommandUtility::importStringInToFileAfterString(
                        $this->element->getFlexFormPath(),
                        [$this->addFieldsToFlexForm() . "\n"],
                        '<el>',
                        0
                    );
                }
            }
        }
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function pluginTemplate()
    {
        if ($this->fields && !file_exists($this->element->getFlexFormPath())) {
            $this->createFlexForm();
        }
        if ($this->fields) {
            $this->setFlexFormFields($this->fields);
            GeneralCreateCommandUtility::importStringInToFileAfterString(
                $this->element->getFlexFormPath(),
                [$this->addFieldsToFlexForm() . "\n"],
                '<el>',
                0
            );
        }
    }

}
