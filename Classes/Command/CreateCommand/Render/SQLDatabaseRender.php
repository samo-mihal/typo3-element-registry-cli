<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class SQLDatabase
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render
 */
class SQLDatabaseRender
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
     * SQLDatabase constructor.
     * @param RenderCreateCommand|null $render
     */
    public function __construct(? RenderCreateCommand $render)
    {
        $this->render = $render;
        $this->fieldsRender = GeneralUtility::makeInstance(FieldsRender::class, $render);
    }

    /**
     * @var array
     */
    protected $dataTypes = [
        'int' => 'int(11) DEFAULT 0 NOT NULL',
        'varchar255' => 'varchar(255) DEFAULT \'\' NOT NULL',
        'text' => 'text',
    ];

    /**
     * @return array
     */
    public function getDataTypes(): array
    {
        return $this->dataTypes;
    }

    /**
     * @return mixed
     */
    public function getIntDataType()
    {
        return $this->getDataTypes()['int'];
    }

    /**
     * @return mixed
     */
    public function getVarchar255DataType()
    {
        return $this->getDataTypes()['varchar255'];
    }

    /**
     * @return mixed
     */
    public function getTextDataType()
    {
        return $this->getDataTypes()['text'];
    }

    /**
     * @param $tableName
     * @return string
     */
    public function newSqlTable($tableName) {
        return "
#
# Table structure for table '" . $tableName . "'
#
CREATE TABLE " . $tableName . " (
    " . $this->fieldsRender->fieldsToSqlTable(). "
);
";
    }

    /**
     * @return string
     * Return CE sql table fields (format string)
     */
    public function getSqlFields()
    {
        $fields = $this->render->getFields();

        if ($fields) {
            $result = [];
            $name = $this->render->getName();
            foreach ($fields->getFields() as $field) {
                $fieldName = $field->getName();
                $fieldType = $field->getType();
                $items = $field->getItems();

                if ($field->exist()) {
                    if ($field->hasSqlDataType()) {
                        if (!self::isAllItemsNumeric($items)) {
                            $result[] = strtolower($name) . '_' . $fieldName.' ' . $this->getVarchar255DataType();
                        } else {
                            $result[] = strtolower($name) . '_' . $fieldName.' ' . $field->getSqlDataType();
                        }
                    }
                } else {
                    throw new InvalidArgumentException('Field "' . $fieldType . '" does not exist.3');
                }
            }

            return implode(",\n    ", $result);
        }
    }

    /**
     * @param $fieldType
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function inlineFields($fieldType)
    {
        $extensionName = $this->render->getExtensionName();

        if ((!empty($this->render->getInlineFields()[$fieldType])) && !$this->render->getFields()->areDefault()) {
            $successStringImported = GeneralCreateCommandUtility::importStringInToFileAfterString(
                'public/typo3conf/ext/' . $extensionName . '/ext_tables.sql',
                [
                    '    ' . $this->fieldsRender->fieldsToSqlTable() . ", \n"
                ],
                'CREATE TABLE tx_contentelementregistry_domain_model_relation (',
                0

            );
            if (!$successStringImported) {
                GeneralCreateCommandUtility::importStringInToFileAfterString(
                    'public/typo3conf/ext/' . $extensionName . '/ext_tables.sql',
                    [
                        $this->newSqlTable('tx_contentelementregistry_domain_model_relation') . "\n"
                    ],
                    '',
                    0

                );
            }
            $output = $this->render->getOutput();
            $output->writeln('<bg=red;options=bold>• Update/Compare Typo3 database. (Inline : ' . $this->render->getName() . ')</>');
        }
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function defaultFields()
    {
        $extensionName = $this->render->getExtensionName();
        $table = $this->render->getTable();
        $fields = $this->render->getFields();

        if (!empty($fields) && !$fields->areDefault()) {
            $successStringImported = GeneralCreateCommandUtility::importStringInToFileAfterString(
                'public/typo3conf/ext/' . $extensionName . '/ext_tables.sql',
                [
                    '    ' . $this->fieldsRender->fieldsToSqlTable() . ", \n"
                ],
                'CREATE TABLE ' . $table . ' (',
                0
            );
            if (!$successStringImported) {
                GeneralCreateCommandUtility::importStringInToFileAfterString(
                    'public/typo3conf/ext/' . $extensionName . '/ext_tables.sql',
                    [
                        $this->newSqlTable($table) . "\n"
                    ],
                    '',
                    0
                );
            }
            $output = $this->render->getOutput();
            $output->writeln('<bg=red;options=bold>• Update/Compare Typo3 database.</>');
        }
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function recordFields()
    {
        $extensionName = $this->render->getExtensionName();
        $table = $this->render->getTable();
        $fields = $this->render->getFields();

        if (!empty($fields) && !$fields->areDefault()) {
            $successStringImported = GeneralCreateCommandUtility::importStringInToFileAfterString(
                'public/typo3conf/ext/' . $extensionName . '/ext_tables.sql',
                [
                    '    ' . $this->fieldsRender->fieldsToSqlTable(false) . ", \n"
                ],
                'CREATE TABLE ' . $table . ' (',
                0
            );
            if (!$successStringImported) {
                GeneralCreateCommandUtility::importStringInToFileAfterString(
                    'public/typo3conf/ext/' . $extensionName . '/ext_tables.sql',
                    [
                        $this->newSqlTable($table, false) . "\n"
                    ],
                    '',
                    0
                );
            }
            $output = $this->render->getOutput();
            $output->writeln('<bg=red;options=bold>• Update/Compare Typo3 database.</>');
        }
    }
}
