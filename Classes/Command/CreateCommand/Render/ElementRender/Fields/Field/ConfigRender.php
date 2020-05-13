<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\Fields\Field;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\AbstractRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\Fields\Field\Config\ItemsRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ConfigRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\Fields\Field
 */
class ConfigRender extends AbstractRender
{
    /**
     * @var FieldObject
     */
    protected $field = null;

    /**
     * @var ItemsRender
     */
    protected $itemsRender = null;

    /**
     * Config constructor.
     * @param ElementRender $element
     * @param FieldObject $field
     */
    public function __construct(ElementRender $element, FieldObject $field)
    {
        parent::__construct($element);
        $this->itemsRender = GeneralUtility::makeInstance(ItemsRender::class, $element, $field);
        $this->field = $field;
    }

    /**
     * @return array
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getConfig()
    {
        $fieldType = $this->field->getType();
        $createCommandCustomData = $this->element->getCreateCommandCustomData();
        $newFieldsConfigs = $createCommandCustomData->newTcaFieldsConfigs($this->field);

        $defaultFieldsConfigs = [
            'input' => $fieldType === 'input' ? $this->getInputConfig() : null,
            'textarea' => $fieldType === 'textarea' ? $this->getTextAreaConfig() : null,
            'check' => $fieldType === 'check' ? $this->getCheckConfig($this->field) : null,
            'radio' => $fieldType === 'radio' ? $this->getRadioConfig($this->field) : null,
            'inline' => $fieldType === 'inline' ? $this->getInlineConfig($this->field) : null,
            'group' => $fieldType === 'group' ? $this->getGroupConfig() : null,
            'select' => $fieldType === 'select' ? $this->getSelectConfig($this->field) : null,
            'fal' => $fieldType === 'fal' ? $this->getFalConfig($this->field) : null,
            'pass_through' => $fieldType === 'pass_through' ? $this->getPassThroughConfig() : null
        ];

        return $newFieldsConfigs ? array_merge($newFieldsConfigs, $defaultFieldsConfigs) : $defaultFieldsConfigs;
    }

    /**
     * @return string
     */
    public function getInputConfig(): string
    {
        return implode(
            "\n" . $this->element->getFields()->getSpacesInTcaColumnConfig(),
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
    public function getPassThroughConfig(): string
    {
        return implode(
            "\n" . $this->element->getFields()->getSpacesInTcaColumnConfig(),
            [
                '[',
                '    \'type\' => \'passthrough\',',
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
            "\n" . $this->element->getFields()->getSpacesInTcaColumnConfig(),
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
            "\n" . $this->element->getFields()->getSpacesInTcaColumnConfig(),
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
            "\n" . $this->element->getFields()->getSpacesInTcaColumnConfig(),
            [
                '\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(',
                '    \'' . $field->getNameInTCA($this->element) . '\',',
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
            "\n" . $this->element->getFields()->getSpacesInTcaColumnConfig(),
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
            "\n" . $this->element->getFields()->getSpacesInTcaColumnConfig(),
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
            "\n" . $this->element->getFields()->getSpacesInTcaColumnConfig(),
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
     * @param string $specialSpaces
     * @return string
     */
    public function getInlineConfig(FieldObject $field, $specialSpaces = ''): string
    {
        $item = $field->getFirstItem();
        $constantPath = $item->getInlineConstantPath($this->element, $field);
        $translationId = $item->getNameInTranslation($this->element, $field);
        if ($this->element->getMainExtension() === $this->element->getExtensionName()) {
            $this->element->translation()->addStringToTranslation(
                $this->element->getTranslationPathFromRoot(),
                $translationId,
                $item->getTitle()
            );

            $specialSpaces = $specialSpaces ? $specialSpaces : $this->element->getFields()->getSpacesInTcaColumnConfig();
            return implode(
                "\n" . $specialSpaces,
                [
                    '[',
                    '    \'type\' => \'inline\',',
                    '    \'foreign_table\' => \'tx_contentelementregistry_domain_model_relation\',',
                    '    \'foreign_field\' => \'content_element\',',
                    '    \'foreign_sortby\' => \'sorting\',',
                    '    \'foreign_match_fields\' => [',
                    '        \'type\' => ' . $constantPath . ',',
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
                    '                        [\'' . $this->element->getTranslationPathShort() . ':' . $translationId . '\', '  . $constantPath . '],',
                    '                    ],',
                    '                    \'default\' => '  . $constantPath . '',
                    '                ],',
                    '            ],',
                    '        ],',
                    '    ],',
                    '],'
                ]
            );
        } else {
            return implode(
                "\n" . $this->element->getFields()->getSpacesInTcaColumnConfig(),
                [
                    '[',
                    '    \'type\' => \'inline\',',
                    '    \'foreign_table\' => \'' . $item->getNewForeignTable($this->element) . '\',',
                    '    \'foreign_field\' => \'' . strtolower($this->element->getStaticName()) .  '\',',
                    '    \'maxitems\' => 9999,',
                    '    \'appearance\' => [',
                    '        \'useSortable\' => true,',
                    '        \'collapseAll\' => 1,',
                    '        \'levelLinksPosition\' => \'top\',',
                    '        \'showSynchronizationLink\' => 1,',
                    '        \'showPossibleLocalizationRecords\' => 1,',
                    '        \'showAllLocalizationLink\' => 1',
                    '    ],',
                    '],'
                ]
            );
        }
    }
}
