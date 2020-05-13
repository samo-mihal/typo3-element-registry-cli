<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class SQLDatabaseRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
class SQLDatabaseRender extends AbstractRender
{
    /**
     * @var FieldsRender
     */
    protected $fieldsRender = null;

    /**
     * SQLDatabaseRender constructor.
     * @param ElementRender $element
     */
    public function __construct(ElementRender $element)
    {
        parent::__construct($element);
        $this->fieldsRender = GeneralUtility::makeInstance(FieldsRender::class, $element);
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
     *
     */
    public function importFieldsToSQLTable()
    {
        $extensionName = $this->element->getExtensionName();
        $table = $this->element->getTable();

        GeneralCreateCommandUtility::importStringInToFileAfterString(
            'public/typo3conf/ext/' . $extensionName . '/ext_tables.sql',
            [
                '    ' . $this->fieldsRender->fieldsToSqlTable() . ", \n"
            ],
            'CREATE TABLE ' . $table . ' (',
            0,
            [
                'newLines' => $this->newSqlTable($table) . "\n",
                'universalStringInFile' => '',
                'linesAfterSpecificString' => 0
            ]

        );
    }

    /**
     * @param $fieldType
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function inlineFields($fieldType)
    {
        if ((!empty($this->element->getInlineFields()[$fieldType])) && !$this->element->getFields()->areDefault()) {
            $this->importFieldsToSQLTable();
        }
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function defaultFields()
    {
        $fields = $this->element->getFields();

        if (!empty($fields) && !$fields->areDefault()) {
            $this->importFieldsToSQLTable();
        }
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function recordFields()
    {
        $fields = $this->element->getFields();

        if (!empty($fields) && !$fields->areDefault()) {
            $this->importFieldsToSQLTable();
        }
    }
}
