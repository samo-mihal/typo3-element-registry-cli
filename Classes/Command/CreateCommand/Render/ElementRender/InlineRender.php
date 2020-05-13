<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Utility\FieldsCreateCommandUtility;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class InlineRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
class InlineRender extends AbstractRender
{
    const CONTENT_ELEMENT_INLINE_RELATION_TABLE = 'tx_contentelementregistry_domain_model_relation';

    /**
     * InlineRender constructor.
     * @param ElementRender $element
     */
    public function __construct(ElementRender $element)
    {
        parent::__construct($element);
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function render()
    {
        $fields = $this->element->getFields();
        if (!empty($fields)) {
            $extensionName = $this->element->getExtensionName();
            $name = $this->element->getName();
            $staticName = $this->element->getStaticName();

            /** @var FieldObject $field */
            foreach ($fields->getFields() as $field) {
                if ($field->isInlineItemsAllowed()) {
                    $firstFieldItemName = $field->getFirstItem()->getName();
                    $firstFieldItemType = $field->getFirstItem()->getType();
                    $newRender = clone $this->element;
                    $newRender->setExtensionName($this->element->getExtensionName());
                    $newRender->setInlineRelativePath($this->element->getInlineRelativePath() . '/' .  $name);
                    $newRender->setName($firstFieldItemName);
                    $newRender->setStaticName($this->element->getStaticName());
                    $newRender->setInlineFields($this->element->getInlineFields());
                    $newRender->setModelNamespace($this->element->getModelNamespace() . '\\' . $name);
                    $newRender->setRelativePathToClass($this->element->getRelativePathToClass());
                    $newRender->setOutput($this->element->getOutput());
                    $newRender->setInput($this->element->getInput());
                    $newRender->setBetweenProtectedsAndGetters('');
                    $newRender->setElementType($this->element->getElementType());
                    if ($extensionName === $this->element->getMainExtension()) {
                        GeneralCreateCommandUtility::importStringInToFileAfterString(
                            'public/typo3conf/ext/' . $this->element->getInlineRelativePath() . '/' . $name . '.php',
                            ['    const CONTENT_RELATION_' . strtoupper($firstFieldItemName) . ' = \'' . str_replace('_', '', $extensionName) . '_' . strtolower($staticName) . '_' . strtolower($firstFieldItemName) . '\';' . "\n\n"],
                            '{',
                            0
                        );
                        $newInlineFields =  GeneralUtility::makeInstance(FieldsCreateCommandUtility::class)->generateObject(
                            $this->element->getInlineFields()[$firstFieldItemType],
                            self::CONTENT_ELEMENT_INLINE_RELATION_TABLE
                        );
                        $newInlineFields->setSpacesInTcaColumnsOverrides('                ');
                        $newRender->setFields($newInlineFields);
                        $newRender->setTable(self::CONTENT_ELEMENT_INLINE_RELATION_TABLE);

                        $newRender->tca()->inlineTemplate();
                        $newRender->typoScript()->inlineMapping();
                    } else {
                        $newInlineTable = 'tx_' . str_replace('_', '', $extensionName) . '_domain_model' . '_' . $newRender->getTcaRelativePath();

                        $newInlineFields = GeneralUtility::makeInstance(FieldsCreateCommandUtility::class)->generateObject(
                            $this->element->getInlineFields()[$firstFieldItemType],
                            $newInlineTable
                        );
                        $newInlineFields->setSpacesInTcaColumn('        ');
                        $newInlineField = new FieldObject();
                        $newInlineField->setName(strtolower($staticName));
                        $newInlineField->setType('pass_through');
                        $newInlineField->setExist(true);
                        $newInlineField->setDefault(false);
                        $newInlineField->setHasModel(false);
                        $newInlineField->setSqlDataType(
                            GeneralUtility::makeInstance(SQLDatabaseRender::class, $this->element)->getIntDataType()
                        );
                        $newInlineFieldsObjectStorage = clone $newInlineFields->getFields();
                        $newInlineFieldsObjectStorage->attach($newInlineField);
                        $newInlineFields->setFields($newInlineFieldsObjectStorage);

                        $newRender->setFields(
                            $newInlineFields
                        );
                        $newRender->setTable($newInlineTable);
                        $newRender->setTcaFieldsPrefix(false);

                        $newRender->translation()->addStringToTranslation(
                            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf',
                            $newInlineTable,
                            $field->getFirstItem()->getTitle()
                        );
                        $newRender->tca()->newInlineTemplate();
                    }

                    $newRender->model()->inlineTemplate();
                    $newRender->translation()->addFieldsTitleToTranslation(
                        'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf'
                    );
                    $newRender->icon()->copyAndRegisterInlineDefaultIcon();
                    $newRender->sqlDatabase()->inlineFields($firstFieldItemType);
                    $newRender->inline()->render();
                }
            }
        }
    }
}
