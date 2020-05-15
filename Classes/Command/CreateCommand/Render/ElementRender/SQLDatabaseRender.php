<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;
use InvalidArgumentException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class SQLDatabaseRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
class SQLDatabaseRender extends AbstractRender
{
    /**
     * Data types
     */
    const INT_11 = 'int(11) DEFAULT 0 NOT NULL';
    const VARCHAR_255 = 'varchar(255) DEFAULT \'\' NOT NULL';
    const TEXT = 'text';

    /**
     * SQLDatabaseRender constructor.
     * @param ElementRender $elementRender
     */
    public function __construct(ElementRender $elementRender)
    {
        parent::__construct($elementRender);
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
    " . GeneralUtility::makeInstance(FieldsRender::class, $this->elementRender)->fieldsToSqlTable(). "
);
";
    }

    /**
     *
     */
    public function importFieldsToSQLTable()
    {
        $extensionName = $this->elementRender->getElement()->getExtensionName();
        $table = $this->elementRender->getElement()->getTable();

        GeneralCreateCommandUtility::importStringInToFileAfterString(
            'public/typo3conf/ext/' . $extensionName . '/ext_tables.sql',
            [
                '    ' . GeneralUtility::makeInstance(FieldsRender::class, $this->elementRender)->fieldsToSqlTable() . ", \n"
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
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function defaultFields()
    {
        if ($this->fields
            && $this->elementRender->getElement()->areAllFieldsDefault() === false)
        {
            $this->importFieldsToSQLTable();
        }
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function recordFields()
    {
        if ($this->fields &&
            !$this->elementRender->getElement()->areAllFieldsDefault())
        {
            $this->importFieldsToSQLTable();
        }
    }
}
