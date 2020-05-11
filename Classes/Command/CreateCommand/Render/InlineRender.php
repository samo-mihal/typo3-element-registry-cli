<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use Digitalwerk\Typo3ElementRegistryCli\Utility\FieldsCreateCommandUtility;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Inline
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render
 */
class InlineRender
{
    const CONTENT_ELEMENT_INLINE_RELATION_TABLE = 'tx_contentelementregistry_domain_model_relation';

    /**
     * @var RenderCreateCommand
     */
    protected $render = null;

    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function render()
    {
        $fields = $this->render->getFields();
        if (!empty($fields)) {
            $extensionName = $this->render->getExtensionName();
            $name = $this->render->getName();
            $staticName = $this->render->getStaticName();

            /** @var FieldObject $field */
            foreach ($fields->getFields() as $field) {
                if ($field->isInlineItemsAllowed()) {
                    $firstFieldItemName = $field->getFirstItem()->getName();
                    $firstFieldItemType = $field->getFirstItem()->getType();
                    $newRender = clone $this->render;
                    $newRender->setExtensionName($this->render->getExtensionName());
                    $newRender->setInlineRelativePath($this->render->getInlineRelativePath() . '/' .  $name);
                    $newRender->setName($firstFieldItemName);
                    $newRender->setStaticName($this->render->getStaticName());
                    $newRender->setInlineFields($this->render->getInlineFields());
                    $newRender->setModelNamespace($this->render->getModelNamespace() . '\\' . $name);
                    $newRender->setRelativePathToClass($this->render->getRelativePathToClass());
                    $newRender->setOutput($this->render->getOutput());
                    $newRender->setInput($this->render->getInput());
                    $newRender->setBetweenProtectedsAndGetters('');
                    $newRender->setElementType($this->render->getElementType());
                    if ($extensionName === $this->render->getMainExtension()) {
                        GeneralCreateCommandUtility::importStringInToFileAfterString(
                            'public/typo3conf/ext/' . $this->render->getInlineRelativePath() . '/' . $name . '.php',
                            ['    const CONTENT_RELATION_' . strtoupper($firstFieldItemName) . ' = \'' . str_replace('_', '', $extensionName) . '_' . strtolower($staticName) . '_' . strtolower($firstFieldItemName) . '\';' . "\n\n"],
                            '{',
                            0
                        );
                        $newInlineFields =  GeneralUtility::makeInstance(FieldsCreateCommandUtility::class)->generateObject(
                            $this->render->getInlineFields()[$firstFieldItemType],
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
                            $this->render->getInlineFields()[$firstFieldItemType],
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
                            GeneralUtility::makeInstance(SQLDatabaseRender::class, $this->render)->getIntDataType()
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
