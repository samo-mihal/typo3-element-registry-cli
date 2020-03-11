<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\TCA\FieldConfigRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use InvalidArgumentException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TCA
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render
 */
class TCARender
{
    /**
     * @var null
     */
    protected $render = null;

    /**
     * TCA constructor.
     * @param RenderCreateCommand $render
     */
    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
    }

    /**
     * @param FieldObject $field
     * @return string
     */
    public function generateFieldInTCA(FieldObject $field): string
    {
        $fieldConfig = GeneralUtility::makeInstance(FieldConfigRender::class, $this->render);
        $fieldName = $field->getName();
        $table = $this->render->getTable();
        $extensionName = $this->render->getExtensionName();
        $name = $this->render->getStaticName();
        $secondDesignation = $this->render->getName();

        return
    '\'' . strtolower($secondDesignation) . '_' . $fieldName . '\' => [
        \'label\' => \'LLL:EXT:' . $extensionName . '/Resources/Private/Language/locallang_db.xlf:' . $table . '.' . str_replace('_','',$extensionName) . '_' . strtolower($name) . '.' . strtolower($secondDesignation) . '_' . $fieldName . '\',
        \'config\' => ' . $fieldConfig->getConfig($field)[$field->getType()] . '
    ],';
    }

    /**
     * @return string
     */
    public function fieldsToShowItemsType()
    {
        $fields = $this->render->getFields();
        if ($fields) {
            $name = $this->render->getName();
            $createdFields = [];

            foreach ($fields->getFields() as $field) {
                $fieldName = $field->getName();
                $fieldType = $field->getType();

                if ($field->isDefault()) {
                    $createdFields[] = $fieldType;
                } elseif (!$field->isDefault()) {
                    $createdFields[] = strtolower($name).'_'.$fieldName;
                } else {
                    throw new InvalidArgumentException('Field "' . $fieldType . '" does not exist.5');
                }
            }

            return implode(', ', $createdFields) . ',';
        }
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
     * @return string
     */
    public function fieldsToColumn()
    {
        $fields = $this->render->getFields();

        if ($fields)
        {
            $extraSpaces2 = '    ';
            $result = [];

            /** @var $field FieldObject  */
            foreach ($fields->getFields() as $field) {
                $fieldType = $field->getType();

                if ($field->exist()) {
                    if (!$field->isDefault()) {
                        $result[] = $this->generateFieldInTCA($field);
                    }
                } else {
                    throw new InvalidArgumentException('Field "' . $fieldType . '" does not exist.4');
                }
            }

            return implode("\n" . $extraSpaces2, $result);
        }
    }

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
    ' . $this->fieldsToColumn() . '
];
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(\'' . $table . '\', $' . lcfirst($this->render->getName()) . 'Columns);
');
        }
    }


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
            \'showitem\' => \'type, ' . $this->fieldsToShowItemsType() . '
                           --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, hidden, starttime, endtime, sys_language_uid, l10n_parent, l10n_diffsource\',';

        if ($this->columnsOverridesFields()) {
            $template[] = '            \'columnsOverrides\' => [
' . $this->columnsOverridesFields('    ') . '
            ],';
        }

        $template[] =
            '        ],
    ],
];

$GLOBALS[\'TCA\'][\'tx_contentelementregistry_domain_model_relation\'] = array_replace_recursive($GLOBALS[\'TCA\'][\'tx_contentelementregistry_domain_model_relation\'], $tempTca);';

        $fieldsToColumn = $this->fieldsToColumn();
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

    public function pageTypeTemplate()
    {
        $table = $this->render->getTable();
        $pageTypeName = $this->render->getName();
        $extensionName = $this->render->getExtensionName();
        $doktype = $this->render->getDoktype();
            file_put_contents('public/typo3conf/ext/' . $this->render->getExtensionName() . '/Configuration/TCA/Overrides/' . $table . '_' . $this->render->getName() . '.php',
                '<?php
declare(strict_types=1);
use Digitalwerk\\' . str_replace(' ','',ucwords(str_replace('_',' ',$extensionName))) . '\Domain\Model;

defined(\'TYPO3_MODE\') or die();

//Add page doktypes
Digitalwerk\DwPageTypes\Utility\PageTypeUtility::addTcaDoktype(Model\\' . $pageTypeName . '::getDoktype());

$tca = [
    \'palettes\' => [
        \'' . lcfirst($pageTypeName) . '\' => [
            \'label\' => \'LLL:EXT:' . $extensionName . '/Resources/Private/Language/locallang_db.xlf:page.type.' . $doktype . '.label\',
            \'showitem\' => \'' . $this->fieldsToShowItemsType() . '\'
        ],
    ],
];

$GLOBALS[\'TCA\'][\'pages\'] = array_replace_recursive($GLOBALS[\'TCA\'][\'pages\'], $tca);

/**
 * tx_contentelementregistry_domain_model_relation new fields
 */
$' . lcfirst($pageTypeName) . 'Columns = [
    ' . $this->fieldsToColumn() . '
];
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(\'' . $table . '\', $' . lcfirst($pageTypeName) . 'Columns);


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    \'pages\',
    \'--div--;LLL:EXT:' . $extensionName . '/Resources/Private/Language/locallang_db.xlf:page.type.' . $doktype . '.label,
                        --palette--;;' . lcfirst($pageTypeName) . '\',
    Model\\' . $pageTypeName . '::getDoktype(),
    \'after:subtitle\'
);');
        }
}
