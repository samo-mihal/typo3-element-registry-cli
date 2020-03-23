<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\Fields;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\Fields\Field\ItemsRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FieldConfigRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\Fields
 */
class FieldConfigRender
{
    /**
     * @var null
     */
    protected $render = null;

    /**
     * @var ItemsRender
     */
    protected $itemsRender = null;

    /**
     * @var FieldRender
     */
    protected $fieldRender = null;

    /**
     * TCA constructor.
     * @param RenderCreateCommand $render
     */
    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
        $this->itemsRender = GeneralUtility::makeInstance(ItemsRender::class, $render);
        $this->fieldRender = GeneralUtility::makeInstance(FieldRender::class, $render);
    }

    /**
     * @param FieldObject $field
     * @return array
     */
    public function getConfig(FieldObject $field)
    {
        $fieldType = $field->getType();
        $mainExtension = $this->render->getMainExtension();
        $mainExtension = str_replace(' ','',ucwords(str_replace('_',' ', $mainExtension)));
        $vendor = $this->render->getVendor();

        $createCommandCustomData = GeneralUtility::makeInstance($vendor . "\\" . $mainExtension . "\\CreateCommandConfig\CreateCommandCustomData");
        $newFieldsConfigs = $createCommandCustomData->newTcaFieldsConfigs($field);

        $defaultFieldsConfigs = [
            'input' => $fieldType === 'input' ? $this->getInputConfig() : null,
            'textarea' => $fieldType === 'textarea' ? $this->getTextAreaConfig() : null,
            'check' => $fieldType === 'check' ? $this->getCheckConfig($field) : null,
            'radio' => $fieldType === 'radio' ? $this->getRadioConfig($field) : null,
            'inline' => $fieldType === 'inline' ? $this->getInlineConfig($field) : null,
            'group' => $fieldType === 'group' ? $this->getGroupConfig() : null,
            'select' => $fieldType === 'select' ? $this->getSelectConfig($field) : null,
            'fal' => $fieldType === 'fal' ? $this->getFalConfig($field) : null
        ];

        return $newFieldsConfigs ? array_merge($newFieldsConfigs, $defaultFieldsConfigs) : $defaultFieldsConfigs;
    }

    /**
     * @return string
     */
    public function getInputConfig(): string
    {
        return implode(
            "\n" . $this->render->getFields()->getSpacesInTcaColumnConfig(),
            [
                '[',
                '    \'type\' => \'input\',',
                '    \'eval\' => \'trim\',',
                '    \'max\' => 255,',
                '],'
            ]
        );
    }

    /**
     * @return string
     */
    public function getTextAreaConfig(): string
    {
        return implode(
            "\n" . $this->render->getFields()->getSpacesInTcaColumnConfig(),
            [
                '[',
                '    \'type\' => \'text\',',
                '    \'enableRichtext\' => true,',
                '],'
            ]
        );
    }

    /**
     * @return string
     */
    public function getGroupConfig(): string
    {
        return implode(
            "\n" . $this->render->getFields()->getSpacesInTcaColumnConfig(),
            [
                '[',
                '    \'type\' => \'group\',',
                '    \'internal_type\' => \'db\',',
                '    \'allowed\' => \'pages\',',
                '    \'size\' => 1,',
                '    \'suggestOptions\' => [',
                '       \'pages\' => [',
                '           \'searchCondition\' => \'doktype=99\',',
                '       ],',
                '    ],',
                '],'
            ]
        );
    }

    /**
     * @param FieldObject $field
     * @return string
     */
    public function getFalConfig(FieldObject $field): string
    {
        return implode(
            "\n" . $this->render->getFields()->getSpacesInTcaColumnConfig(),
            [
                '\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(',
                '    \'' . $this->fieldRender->fieldNameInTca($field) . '\',',
                '    [',
                '        \'appearance\' => [',
                '           \'createNewRelationLinkTitle\' => \'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference\',',
                '        ],',
                '        \'overrideChildTca\' => [',
                '           \'types\' => [',
                '               \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [',
                '                   \'showitem\' => \'',
                '                   --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,',
                '                   --palette--;;filePalette\'',
                '               ],',
                '           ],',
                '        ],',
                '    ],',
                '    $GLOBALS[\'TYPO3_CONF_VARS\'][\'GFX\'][\'imagefile_ext\']',
                '),'
            ]
        );
    }

    /**
     * @param FieldObject $field
     * @return string
     */
    public function getCheckConfig(FieldObject $field): string
    {
        return implode(
            "\n" . $this->render->getFields()->getSpacesInTcaColumnConfig(),
            [
                '[',
                '    \'type\' => \'check\',',
                '    \'items\' => [',
                '    ' . $this->itemsRender->itemsToTcaFromField($field),
                '    ],',
                '     \'cols\' => \'3\',',
                '],'
            ]
        );
    }

    /**
     * @param FieldObject $field
     * @return string
     */
    public function getSelectConfig(FieldObject $field): string
    {
        return implode(
            "\n" . $this->render->getFields()->getSpacesInTcaColumnConfig(),
            [
                '[',
                '    \'type\' => \'select\',',
                '    \'renderType\' => \'selectSingle\',',
                '    \'items\' => [',
                '       [\'\', 0],',
                '    ' . $this->itemsRender->itemsToTcaFromField($field),
                '    ],',
                '     \'cols\' => \'3\',',
                '],'
            ]
        );
    }

    /**
     * @param FieldObject $field
     * @return string
     */
    public function getRadioConfig(FieldObject $field): string
    {
        return implode(
            "\n" . $this->render->getFields()->getSpacesInTcaColumnConfig(),
            [
                '[',
                '    \'type\' => \'radio\',',
                '    \'items\' => [',
                '    ' . $this->itemsRender->itemsToTcaFromField($field),
                '    ],',
                '],'
            ]
        );
    }

    /**
     * @param FieldObject $field
     * @return string
     */
    public function getInlineConfig(FieldObject $field): string
    {
        $extensionName = $this->render->getExtensionName();
        $fieldName = strtolower($field->getName());
        $pathToModel = '\\' . $this->render->getModelNamespace() . '\\' . $this->render->getName();
        $item = $field->getFirstItem();
        $table = $this->render->getTable();


        $this->render->translation()->addStringToTranslation(
            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf',
           $this->render->getTable() . '.' . str_replace('_', '', $extensionName) . '_' . $fieldName . '_' . strtolower($item->getName()),
            str_replace('-', ' ', $item->getTitle())
        );
        $itemName = $item->getName();

        return implode(
            "\n" . $this->render->getFields()->getSpacesInTcaColumnConfig(),
            [
                '[',
                '    \'type\' => \'inline\',',
                '    \'foreign_table\' => \'tx_contentelementregistry_domain_model_relation\',',
                '    \'foreign_field\' => \'content_element\',',
                '    \'foreign_sortby\' => \'sorting\',',
                '    \'foreign_match_fields\' => [',
                '        \'type\' => ' . $pathToModel . '::CONTENT_RELATION_' . strtoupper($itemName) . ',',
                '    ],',
                '    \'maxitems\' => 9999,',
                '    \'appearance\' => [',
                '        \'useSortable\' => true,',
                '        \'collapseAll\' => 1,',
                '        \'levelLinksPosition\' => \'top\',',
                '        \'showSynchronizationLink\' => 1,',
                '        \'showPossibleLocalizationRecords\' => 1,',
                '        \'showAllLocalizationLink\' => 1',
                '    ],',
                '    \'overrideChildTca\' => [',
                '        \'columns\' => [',
                '            \'type\' => [',
                '                \'config\' => [',
                '                    \'items\' => [',
                '                        [\'LLL:EXT:' . $extensionName . '/Resources/Private/Language/locallang_db.xlf:' . $table . '.' . str_replace('_', '', $extensionName) . '_' . strtolower($fieldName) . '_' . strtolower($itemName) . '\', '  . $pathToModel . '::CONTENT_RELATION_' . strtoupper($itemName) . '],',
                '                    ],',
                '                    \'default\' => '  . $pathToModel . '::CONTENT_RELATION_' . strtoupper($itemName) . '',
                '                ],',
                '            ],',
                '        ],',
                '    ],',
                '],'
            ]
        );
    }
}
