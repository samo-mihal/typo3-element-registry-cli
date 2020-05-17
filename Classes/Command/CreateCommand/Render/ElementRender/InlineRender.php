<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\ElementSetup;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;

/**
 * Class InlineRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
class InlineRender extends AbstractRender
{
    const CONTENT_ELEMENT_INLINE_RELATION_TABLE = 'tx_contentelementregistry_domain_model_relation';

    /**
     * InlineRender constructor.
     * @param ElementRender $elementRender
     */
    public function __construct(ElementRender $elementRender)
    {
        parent::__construct($elementRender);
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function render()
    {
        $fields = $this->fields;
        if (!empty($fields)) {
            $extensionName = $this->elementRender->getElement()->getExtensionName();
            $name = $this->elementRender->getElement()->getName();
            $staticName = $this->elementRender->getElement()->getStaticName();

            /** @var FieldObject $field */
            foreach ($fields as $field) {
                if ($field->isInlineItemsAllowed()) {
                    $firstFieldItemName = $field->getFirstItem()->getName();
                    $firstFieldItemType = $field->getFirstItem()->getType();

                    $newRender = new ElementRender();
                    $newElementObject = clone $this->elementRender->getElement();
                    $newElementObject->setModelPath($this->elementRender->getElement()->getModelPath() . '/' .  $name);
                    $newElementObject->setName($firstFieldItemName);
                    $newElementObject->setModelNamespace($this->elementRender->getElement()->getModelNamespace() . '\\' . $name);
                    GeneralCreateCommandUtility::importStringInToFileAfterString(
                        $this->elementRender->getElement()->getModelPath() . '/' . $name . '.php',
                        ['    const CONTENT_RELATION_' . strtoupper($firstFieldItemName) . ' = \'' . str_replace('_', '', $extensionName) . '_' . strtolower($staticName) . '_' . strtolower($firstFieldItemName) . '\';' . "\n\n"],
                        '{',
                        0
                    );
                    if ($field->isDefault() && $this->elementRender->getElement()->getType() === ElementSetup::CONTENT_ELEMENT) {
                        $newInlineFields = $this->elementRender->getElement()->getInlineFields()[$firstFieldItemType];
                        $newElementObject->setFieldsSpacesInTcaColumnsOverrides('                ');
                        $newElementObject->setFields($newInlineFields);
                        $newElementObject->setTable(self::CONTENT_ELEMENT_INLINE_RELATION_TABLE);
                        $newRender->setElement($newElementObject);
                        $newRender->tca()->inlineTemplate();
                        $newRender->typoScript()->inlineMapping();
                    } else {
                        $newInlineTable = 'tx_' . str_replace('_', '', $extensionName) . '_domain_model' . '_' . $newElementObject->getTCANameFromModelPath();

                        $newInlineFields = $this->elementRender->getElement()->getInlineFields()[$firstFieldItemType];
                        $newElementObject->setFieldsSpacesInTcaColumn('        ');
                        $newInlineField = new FieldObject();
                        $newInlineField->setName(strtolower($staticName));
                        $newInlineField->setType('pass_through');
                        $newInlineField->setExist(true);
                        $newInlineField->setDefault(false);
                        $newInlineField->setHasModel(false);
                        $newInlineField->setSqlDataType(SQLDatabaseRender::INT_11);
                        $newInlineFields->attach($newInlineField);
                        $newElementObject->setFields($newInlineFields);
                        $newElementObject->setTable($newInlineTable);
                        $newElementObject->setTcaFieldsPrefix(false);
                        $newRender->setElement($newElementObject);
                        $newRender->translation()->addStringToTranslation(
                            $newInlineTable,
                            $field->getFirstItem()->getTitle()
                        );
                        $newRender->tca()->newInlineTemplate();
                    }
                    $newRender->model()->inlineTemplate();
                    $newRender->translation()->addFieldsTitleToTranslation();
                    $newRender->icon()->copyAndRegisterInlineDefaultIcon();
                    $newRender->sqlDatabase()->recordFields();
                    $newRender->inline()->render();
                }
            }
        }
    }
}
