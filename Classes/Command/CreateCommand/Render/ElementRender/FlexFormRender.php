<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\FlexFormFieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
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
        return implode("\n                    ", $result);
    }

    /**
     * @param $file
     */
    public function createFlexForm($file)
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
                    ' . $this->addFieldsToFlexForm() . '
                </el>
            </ROOT>
        </sDEF>
    </sheets>
</T3DataStructure>
';
        file_put_contents($file, $CEFlexFormContent);
    }

    public function contentElementTemplate()
    {
        $fields = $this->fields;
        $extensionName = $this->elementRender->getElement()->getExtensionName();
        $name = $this->elementRender->getElement()->getName();

        if ($fields) {
            /** @var FieldObject $field */
            foreach ($fields as $field) {
                if ($field->isFlexFormItemsAllowed())
                {
                    $this->setFlexFormFields(
                        $this->elementRender->getElement()->getInlineFields()[$field->getFirstItem()->getType()]
                    );
                    if (!file_exists('public/typo3conf/ext/' . $extensionName . '/Configuration/FlexForms/ContentElement')) {
                        mkdir('public/typo3conf/ext/' . $extensionName . '/Configuration/FlexForms/ContentElement/', 0777, true);
                    }
                    $this->createFlexForm(
                        "public/typo3conf/ext/" . $extensionName . "/Configuration/FlexForms/ContentElement/" . str_replace('_', '', $extensionName) . "_" . strtolower($name) . '.xml'
                    );
                }
            }
        }
    }

    public function pluginTemplate()
    {
        if ($this->fields) {
            $extensionName = $this->elementRender->getElement()->getExtensionName();
            $name = $this->elementRender->getElement()->getName();
            $this->setFlexFormFields($this->fields);
            if (!file_exists('public/typo3conf/ext/' . $extensionName . '/Configuration/FlexForms')) {
                mkdir('public/typo3conf/ext/' . $extensionName . '/Configuration/FlexForms', 0777, true);
            }
            $this->createFlexForm(
                "public/typo3conf/ext/" . $extensionName . "/Configuration/FlexForms/"  . $name . '.xml'
            );
        }
    }

}
