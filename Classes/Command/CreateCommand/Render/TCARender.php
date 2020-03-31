<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TCA
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render
 */
class TCARender
{
    /**
     * @var RenderCreateCommand
     */
    protected $render = null;

    /**
     * @var FieldsRender
     */
    protected $fieldsRender = null;

    /**
     * TCA constructor.
     * @param RenderCreateCommand $render
     */
    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
        $this->fieldsRender = GeneralUtility::makeInstance(FieldsRender::class, $render);
    }

    /**
     * @param string $extraSpaces
     * @return string
     */
    public function columnsOverridesFields($extraSpaces = '')
    {
        $fields = $this->render->getFields();

        if ($fields) {
            $table = $this->render->getTable();
            $staticName = $this->render->getStaticName();
            $name = $this->render->getName();
            $defaultFieldsWithAnotherTitle = [];

            foreach ($fields->getFields() as $field) {
                $fieldName = $field->getName();
                $fieldType = $field->getType();
                $fieldTitle = $field->getTitle();
                $extensionName = $this->render->getExtensionName();

                if ($fieldTitle !== $field->getDefaultTitle() && $field->isDefault())
                {
                        $defaultFieldsWithAnotherTitle[] =
                            $extraSpaces . '            \''.$fieldType.'\' => [
                '.$extraSpaces.'\'label\' => \'LLL:EXT:' . $extensionName . '/Resources/Private/Language/locallang_db.xlf:' . $table . '.' . str_replace('_', '', $extensionName) . '_'.strtolower($staticName).'.'. strtolower($name).'_'. strtolower($fieldName).'\',
            '.$extraSpaces.'],';
                }
            }

            return implode("\n", $defaultFieldsWithAnotherTitle);
        }
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function contentElementTemplate()
    {
        $table = $this->render->getTable();
        if ($this->render->getFields() && !$this->render->getFields()->areDefault()) {
            file_put_contents('public/typo3conf/ext/' . $this->render->getExtensionName() . '/Configuration/TCA/Overrides/' . $table . '_' . $this->render->getName() . '.php',
                '<?php
defined(\'TYPO3_MODE\') or die();

/**
 * ' . $table . ' new fields
 */
$' . lcfirst($this->render->getName()) . 'Columns = [
    ' . $this->fieldsRender->fieldsToColumn() . '
];
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(\'' . $table . '\', $' . lcfirst($this->render->getName()) . 'Columns);
');
        }
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function inlineTemplate()
    {
        $staticName = $this->render->getStaticName();
        $name = $this->render->getName();
        $pathToModel = '\\' . $this->render->getModelNamespace();

        $template [] = '<?php
defined(\'TYPO3_MODE\') or die();

$tempTca = [
    \'ctrl\' => [
        \'typeicon_classes\' => [
            ' . $pathToModel . '::CONTENT_RELATION_'.strtoupper($name).' => ' . $pathToModel . '::CONTENT_RELATION_'.strtoupper($name).',
        ],
    ],
    \'types\' => [
        ' . $pathToModel . '::CONTENT_RELATION_'.strtoupper($name).' => [
            \'showitem\' => \'type, ' . $this->fieldsRender->fieldsToType() . '
                           --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, hidden, starttime, endtime, sys_language_uid, l10n_parent, l10n_diffsource\',';

        $columnsOverridesFields = $this->columnsOverridesFields('    ');
        if ($columnsOverridesFields) {
            $template[] = '            \'columnsOverrides\' => [
' . $columnsOverridesFields . '
            ],';
        }

        $template[] =
            '        ],
    ],
];

$GLOBALS[\'TCA\'][\'tx_contentelementregistry_domain_model_relation\'] = array_replace_recursive($GLOBALS[\'TCA\'][\'tx_contentelementregistry_domain_model_relation\'], $tempTca);';

        $fieldsToColumn = $this->fieldsRender->fieldsToColumn();
        if ($fieldsToColumn) {
            $template[] = '
/**
 * tx_contentelementregistry_domain_model_relation new fields
 */
$'.lcfirst($name).'Columns = [
    ' . $fieldsToColumn . '
];
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(\'tx_contentelementregistry_domain_model_relation\', $'.lcfirst($name).'Columns);';
        }

        if ($this->render->getFields()) {
            file_put_contents(
                'public/typo3conf/ext/' . $this->render->getExtensionName() . '/Configuration/TCA/Overrides/tx_contentelementregistry_domain_model_relation_' . $staticName . '_' . $name . '.php',
                implode("\n", $template)
            );
        }
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function pageTypeTemplate()
    {
        $table = $this->render->getTable();
        $pageTypeName = $this->render->getName();
        $extensionName = $this->render->getExtensionName();
        $doktype = $this->render->getDoktype();
            file_put_contents('public/typo3conf/ext/' . $this->render->getExtensionName() . '/Configuration/TCA/Overrides/' . $table . '_' . $this->render->getName() . '.php',
                '<?php
declare(strict_types=1);
defined(\'TYPO3_MODE\') or die();

Digitalwerk\Typo3ElementRegistryCli\Utility\Typo3ElementRegistryCliUtility::addTcaDoktype(\\' . $this->render->getVendor() . '\\' . $this->render->getExtensionNameSpaceFormat($extensionName) . '\Domain\Model\\' . $pageTypeName . '::getDoktype());

$tca = [
    \'palettes\' => [
        \'' . lcfirst($pageTypeName) . '\' => [
            \'label\' => \'LLL:EXT:' . $extensionName . '/Resources/Private/Language/locallang_db.xlf:page.type.' . $doktype . '.label\',
            \'showitem\' => \'' . $this->fieldsRender->fieldsToPalette() . '\'
        ],
    ],
];

$GLOBALS[\'TCA\'][\'pages\'] = array_replace_recursive($GLOBALS[\'TCA\'][\'pages\'], $tca);

/**
 * tx_contentelementregistry_domain_model_relation new fields
 */
$' . lcfirst($pageTypeName) . 'Columns = [
    ' . $this->fieldsRender->fieldsToColumn() . '
];
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(\'' . $table . '\', $' . lcfirst($pageTypeName) . 'Columns);


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    \'pages\',
    \'--div--;LLL:EXT:' . $extensionName . '/Resources/Private/Language/locallang_db.xlf:page.type.' . $doktype . '.label,
                        --palette--;;' . lcfirst($pageTypeName) . '\',
    \\' . $this->render->getVendor() . '\\' . $this->render->getExtensionNameSpaceFormat($extensionName) . '\Domain\Model\\' . $pageTypeName . '::getDoktype(),
    \'after:subtitle\'
);');
        }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function recordTemplate()
    {
        $table = $this->render->getTable();
        $name = $this->render->getName();
        $extensionName = $this->render->getExtensionName();
        file_put_contents('public/typo3conf/ext/' . $this->render->getExtensionName() . '/Configuration/TCA/' . $table . '.php',
            '<?php

return [
    \'ctrl\' => [
        \'title\' => \'LLL:EXT:' . $extensionName . '/Resources/Private/Language/locallang_db.xlf:' . $this->render->getTable() . '\',
        //NEED CHANGE
        \'label\' => \'title\',
        \'tstamp\' => \'tstamp\',
        \'crdate\' => \'crdate\',
        \'cruser_id\' => \'cruser_id\',
        \'versioningWS\' => true,
        \'languageField\' => \'sys_language_uid\',
        \'transOrigPointerField\' => \'l10n_parent\',
        \'transOrigDiffSourceField\' => \'l10n_diffsource\',
        \'delete\' => \'deleted\',
        \'sortby\' => \'sorting\',
        \'enablecolumns\' => [
            \'disabled\' => \'hidden\',
            \'starttime\' => \'starttime\',
            \'endtime\' => \'endtime\',
        ],
        //NEED CHANGE
        \'thumbnail\' => \'icon\',
        //NEED CHANGE
        \'searchFields\' => \'title\',
        \'typeicon_classes\' => [
            \'default\' => \'' . $name . '\',
        ],
    ],

    \'types\' => [
        \'0\' => [
            \'showitem\' => \'--palette--;; ' . strtolower($name) . 'Default,
                          --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, hidden, starttime, endtime, sys_language_uid, l10n_parent, l10n_diffsource\'
        ],
    ],
    \'palettes\' => [
        \'' . strtolower($name) . 'Default\' => [
            \'showitem\' => \'' . $this->fieldsRender->fieldsToPalette() . '\',
        ],
    ],
    \'columns\' => [
        \'sys_language_uid\' => [
            \'exclude\' => true,
            \'label\' => \'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language\',
            \'config\' => [
                \'type\' => \'select\',
                \'renderType\' => \'selectSingle\',
                \'special\' => \'languages\',
                \'items\' => [
                    [
                        \'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages\',
                        -1,
                        \'flags-multiple\'
                    ]
                ],
                \'default\' => 0,
            ],
        ],
        \'l10n_parent\' => [
            \'displayCond\' => \'FIELD:sys_language_uid:>:0\',
            \'exclude\' => true,
            \'label\' => \'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent\',
            \'config\' => [
                \'type\' => \'select\',
                \'renderType\' => \'selectSingle\',
                \'default\' => 0,
                \'items\' => [
                    [\'\', 0],
                ],
                \'foreign_table\' => \'tx_dwboilerplate_domain_model_person\',
                \'foreign_table_where\' => \'AND tx_dwboilerplate_domain_model_person.pid=###CURRENT_PID### AND tx_dwboilerplate_domain_model_person.sys_language_uid IN (-1,0)\',
            ],
        ],
        \'l10n_diffsource\' => [
            \'config\' => [
                \'type\' => \'passthrough\',
            ],
        ],
        \'t3ver_label\' => [
            \'label\' => \'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel\',
            \'config\' => [
                \'type\' => \'input\',
                \'size\' => 30,
                \'max\' => 255,
            ],
        ],
        \'hidden\' => [
            \'exclude\' => true,
            \'label\' => \'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden\',
            \'config\' => [
                \'type\' => \'check\',
                \'items\' => [
                    \'1\' => [
                        \'0\' => \'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled\'
                    ]
                ],
            ],
        ],
        \'starttime\' => [
            \'exclude\' => true,
            \'behaviour\' => [
                \'allowLanguageSynchronization\' => true
            ],
            \'label\' => \'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime\',
            \'config\' => [
                \'type\' => \'input\',
                \'renderType\' => \'inputDateTime\',
                \'size\' => 13,
                \'eval\' => \'datetime\',
                \'default\' => 0,
            ],
        ],
        \'endtime\' => [
            \'exclude\' => true,
            \'behaviour\' => [
                \'allowLanguageSynchronization\' => true
            ],
            \'label\' => \'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime\',
            \'config\' => [
                \'type\' => \'input\',
                \'renderType\' => \'inputDateTime\',
                \'size\' => 13,
                \'eval\' => \'datetime\',
                \'default\' => 0,
                \'range\' => [
                    \'upper\' => mktime(0, 0, 0, 1, 1, 2038)
                ],
            ],
        ],
        ' . $this->fieldsRender->fieldsToColumn() . '
    ],
];');
    }
}
