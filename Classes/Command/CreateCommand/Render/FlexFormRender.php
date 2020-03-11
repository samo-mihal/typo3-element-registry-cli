<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\FlexFormFieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\FieldsObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use Digitalwerk\Typo3ElementRegistryCli\Utility\FieldsCreateCommandUtility;
use InvalidArgumentException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FlexForm
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render
 */
class FlexFormRender
{
    /**
     * @var FieldsObject
     */
    protected $flexFormFields = null;

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
     * @return FieldsObject
     */
    public function getFlexFormFields(): FieldsObject
    {
        return $this->flexFormFields;
    }

    /**
     * @param FieldsObject $flexFormFields
     */
    public function setFlexFormFields(FieldsObject $flexFormFields): void
    {
        $this->flexFormFields = $flexFormFields;
    }

    /**
     * @return string
     */
    public function addFieldsToFlexForm()
    {
        $fields = $this->getFlexFormFields();
        $name = $this->render->getName();
        $extensionName = $this->render->getExtensionName();
        $flexFormFieldTypes = GeneralUtility::makeInstance(FlexFormFieldTypesConfig::class)->getFlexFormFieldTypes();
        $result = [];

        /** @var FieldObject $field */
        foreach ($fields->getFields() as $field) {
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

                $this->render->translation()->addStringToTranslation(
                    'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf',
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
        $fields = $this->render->getFields();
        $extensionName = $this->render->getExtensionName();
        $name = $this->render->getName();

        if ($fields) {
            /** @var FieldObject $field */
            foreach ($fields->getFields() as $field) {
                if ($field->isFlexFormItemsAllowed())
                {
                    $this->setFlexFormFields(
                        GeneralUtility::makeInstance(FieldsCreateCommandUtility::class)->generateObject(
                            $this->render->getInlineFields()[$field->getFirstItem()->getType()],
                            ''
                        )
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
        $extensionName = $this->render->getExtensionName();
        $name = $this->render->getName();

        $this->setFlexFormFields($this->render->getFields());
        if (!file_exists('public/typo3conf/ext/' . $extensionName . '/Configuration/FlexForms')) {
            mkdir('public/typo3conf/ext/' . $extensionName . '/Configuration/FlexForms', 0777, true);
        }
        $this->createFlexForm(
            "public/typo3conf/ext/" . $extensionName . "/Configuration/FlexForms/"  . $name . '.xml'
        );
    }

}
