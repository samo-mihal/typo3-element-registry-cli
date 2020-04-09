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
     * @var string
     */
    protected $spacesInTypoScriptMapping = '            ';

    /**
     * @var string
     */
    protected $spacesInTcaPalette = '            ';

    /**
     * @var string
     */
    protected $spacesInTcaColumnsOverrides = '            ';

    /**
     * @return string
     */
    public function getSpacesInTypoScriptMapping(): string
    {
        return $this->spacesInTypoScriptMapping;
    }

    /**
     * @param string|null $spacesInTypoScriptMapping
     */
    public function setSpacesInTypoScriptMapping(? string $spacesInTypoScriptMapping): void
    {
        $this->spacesInTypoScriptMapping = $spacesInTypoScriptMapping;
    }

    /**
     * @return string
     */
    public function getSpacesInTcaPalette(): string
    {
        return $this->spacesInTcaPalette;
    }

    /**
     * @param string|null $spacesInTcaPalette
     */
    public function setSpacesInTcaPalette(? string $spacesInTcaPalette): void
    {
        $this->spacesInTcaPalette = $spacesInTcaPalette;
    }

    /**
     * @return string
     */
    public function getSpacesInTcaColumnsOverrides(): string
    {
        return $this->spacesInTcaColumnsOverrides;
    }

    /**
     * @return string
     */
    public function getSpacesInTcaColumnsOverridesConfig(): string
    {
        return $this->spacesInTcaColumnsOverrides . self::TAB;
    }

    /**
     * @param string|null $spacesInTcaColumnsOverrides
     */
    public function setSpacesInTcaColumnsOverrides(? string $spacesInTcaColumnsOverrides): void
    {
        $this->spacesInTcaColumnsOverrides = $spacesInTcaColumnsOverrides;
    }

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
