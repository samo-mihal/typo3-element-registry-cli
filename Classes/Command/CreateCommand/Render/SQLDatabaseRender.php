<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;
use Symfony\Component\Console\Exception\InvalidArgumentException;

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
     * SQLDatabase constructor.
     * @param RenderCreateCommand|null $render
     */
    public function __construct(? RenderCreateCommand $render)
    {
        $this->render = $render;
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
    " . $this->getSqlFields(). "
);";
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
     * @param $items
     * @return mixed
     */
    public function isAllItemsNumeric($items)
    {
        foreach ($items as $item) {
            if (!is_numeric($item->getValue())) {
                return false;
                break;
            }
        }

        return true;
    }

    /**
     * @param $fieldType
     */
    public function inlineFields($fieldType)
    {
        $extensionName = $this->render->getExtensionName();

        if ((!empty($this->render->getInlineFields()[$fieldType])) && !$this->render->getFields()->areDefault()) {
            $successStringImported = GeneralCreateCommandUtility::importStringInToFileAfterString(
                'public/typo3conf/ext/' . $extensionName . '/ext_tables.sql',
                [
                    '    ' . $this->getSqlFields(). ", \n"
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

    public function fields()
    {
        $extensionName = $this->render->getExtensionName();
        $table = $this->render->getTable();
        $fields = $this->render->getFields();

        if (!empty($fields) && !$fields->areDefault()) {
            $successStringImported = GeneralCreateCommandUtility::importStringInToFileAfterString(
                'public/typo3conf/ext/' . $extensionName . '/ext_tables.sql',
                [
                    '    ' . $this->getSqlFields(). ", \n"
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
}
