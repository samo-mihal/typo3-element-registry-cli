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
}
