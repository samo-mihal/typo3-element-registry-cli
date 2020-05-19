<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\Fields\Field;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\AbstractRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\Fields\Field\Config\ItemsRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ConfigRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\Element\Field
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
     * @var string
     */
    protected $spacesInTCAColumn = '';

    /**
     * Config constructor.
     * @param ElementRender $elementRender
     * @param FieldObject $field
     */
    public function __construct(ElementRender $elementRender, FieldObject $field)
    {
        parent::__construct($elementRender);
        $this->itemsRender = GeneralUtility::makeInstance(ItemsRender::class, $elementRender, $field);
        $this->field = $field;
        $this->spacesInTCAColumn = $this->elementRender->getElement()->getFieldsSpacesInTcaColumnConfig();
    }

    /**
     * @return array
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getConfig()
    {
        $fieldType = $this->field->getType();
        $createCommandCustomData = $this->elementRender->getElement()->getCreateCommandCustomData();
        $newFieldsConfigs = $createCommandCustomData->newTcaFieldsConfigs($this->element, $this->field);

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
            "\n" . $this->spacesInTCAColumn,
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
            "\n" . $this->spacesInTCAColumn,
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
            "\n" . $this->spacesInTCAColumn,
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
            "\n" . $this->spacesInTCAColumn,
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
            "\n" . $this->spacesInTCAColumn,
            [
                '\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(',
                '    \'' . $field->getNameInTCA($this->elementRender->getElement()) . '\',',
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
            "\n" . $this->spacesInTCAColumn,
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
     * @param string $specialSpaces
     * @return string
     */
    public function getSelectConfig(FieldObject $field, $specialSpaces = ''): string
    {
        $specialSpaces = $specialSpaces ? $specialSpaces : $this->spacesInTCAColumn;
        return implode(
            "\n" . $specialSpaces,
            [
                '[',
                '    \'type\' => \'select\',',
                '    \'renderType\' => \'selectSingle\',',
                '    \'items\' => [',
                '        [\'\', 0],',
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
            "\n" . $this->spacesInTCAColumn,
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
        $constantPath = $item->getInlineConstantPath($this->elementRender->getElement());
        $translationId = $item->getNameInTranslation($this->elementRender->getElement(), $field);
        if ($field->isDefault() && $this->extensionName === $this->element->getMainExtension()) {
            $this->elementRender->translation()->addStringToTranslation(
                $translationId,
                $item->getTitle()
            );

            $specialSpaces = $specialSpaces ? $specialSpaces : $this->spacesInTCAColumn;
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
                    '                        [\'' . $this->elementRender->getElement()->getTranslationPathShort() . ':' . $translationId . '\', '  . $constantPath . '],',
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
                "\n" . $this->spacesInTCAColumn,
                [
                    '[',
                    '    \'type\' => \'inline\',',
                    '    \'foreign_table\' => \'' . $item->getNewForeignTable($this->elementRender->getElement()) . '\',',
                    '    \'foreign_field\' => \'' . strtolower($this->elementRender->getElement()->getStaticName()) .  '\',',
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
