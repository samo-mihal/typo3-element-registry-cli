<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class FieldsObject
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object
 */
class FieldsObject
{
    const TAB = '    ';

    /**
     * @var ObjectStorage<\Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject>
     */
    protected $fields = null;

    /**
     * @var bool
     */
    protected $areDefault = false;

    /**
     * @var string
     */
    protected $spacesInTcaColumn = '    ';

    /**
     * @return string
     */
    public function getSpacesInTcaColumn(): string
    {
        return $this->spacesInTcaColumn;
    }

    /**
     * @return string
     */
    public function getSpacesInTcaColumnConfig(): string
    {
        return $this->spacesInTcaColumn . self::TAB;
    }

    /**
     * @return string
     */
    public function getSpacesInTcaColumnConfigItems(): string
    {
        return $this->getSpacesInTcaColumnConfig() . self::TAB;
    }

    /**
     * @param string|null $spacesInTcaColumn
     */
    public function setSpacesInTcaColumn(? string $spacesInTcaColumn): void
    {
        $this->spacesInTcaColumn = $spacesInTcaColumn;
    }

    /**
     * @return ObjectStorage
     */
    public function getFields(): ObjectStorage
    {
        return $this->fields;
    }

    /**
     * @param ObjectStorage $fields
     */
    public function setFields(ObjectStorage $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @return bool
     */
    public function areDefault(): bool
    {
        return $this->areDefault;
    }

    /**
     * @param bool $areDefault
     */
    public function setAreDefault(bool $areDefault)
    {
        $this->areDefault = $areDefault;
    }
}
