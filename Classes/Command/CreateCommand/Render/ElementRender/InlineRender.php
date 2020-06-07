<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\ElementSetup;

/**
 * Class InlineRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
class InlineRender extends AbstractRender
{
    /**
     * Default inline table
     */
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
                    $newElementObject->setType(ElementSetup::INLINE);
                    $newElementObject->setTcaFieldsPrefix(false);
                    $newElementObject->setModelDirPath($this->elementRender->getElement()->getModelDirPath() . '/' .  $name);
                    $newElementObject->setName($firstFieldItemName);
                    $newElementObject->setModelNamespace($this->elementRender->getElement()->getModelNamespace() . '\\' . $name);

                    if ($field->isDefault()) {
                        $this->importStringRender->importStringInToFileAfterString(
                            $newElementObject->getModelDirPath() . '.php',
                            ElementObject::FIELDS_TAB . 'const CONTENT_RELATION_' .
                            strtoupper($firstFieldItemName) . ' = \'' . str_replace('_', '', $extensionName) .
                            '_' . strtolower($staticName) . '_' . strtolower($firstFieldItemName) . '\';' . "\n\n",
                            '{',
                            0
                        );
                        $newInlineFields = $this->elementRender->getElement()->getInlineFields()[$firstFieldItemType];
                        $newElementObject->setFieldsSpacesInTcaColumnsOverrides('                ');
                        $newElementObject->setFields($newInlineFields);
                        $newElementObject->setTable(self::CONTENT_ELEMENT_INLINE_RELATION_TABLE);
                        $newRender->setElement($newElementObject);
                        $newRender->tca()->inlineTemplate();
                        $newRender->typoScript()->inlineMapping();
                    } else {
                        $newInlineTable = 'tx_' . str_replace('_', '', $extensionName) . '_domain_model' . '_' . $newElementObject->getNamesFromModelPath();

                        $newInlineFields = $this->elementRender->getElement()->getInlineFields()[$firstFieldItemType];
                        $newElementObject->setFieldsSpacesInTcaColumn('        ');
                        $newInlineFields->attach($this->createForeignField($field));
                        $newElementObject->setFields($newInlineFields);
                        $newElementObject->setTable($newInlineTable);
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

    /**
     * @param FieldObject $field
     * @return FieldObject
     */
    private function createForeignField(FieldObject $field)
    {
        $newField = new FieldObject();
        $newField->setName($field->getFirstItem()->getAdditionalInformation()['foreign_field']);
        $newField->setType('pass_through');
        $newField->setExist(true);
        $newField->setDefault(false);
        $newField->setHasModel(false);
        $newField->setSqlDataType(SQLDatabaseRender::INT_11);

        return $newField;
    }
}
