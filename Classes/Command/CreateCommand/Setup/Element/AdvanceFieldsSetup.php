<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element;

/**
 * Class AdvanceFieldsSetup
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element
 */
class AdvanceFieldsSetup
{
    /**
     * @var array
     */
    public static $advanceFields = [];

    /**
     * @return array
     */
    public static function getAdvanceFields(): array
    {
        return self::$advanceFields;
    }

    /**
     * @param array $advanceFields
     */
    public static function setAdvanceFields(array $advanceFields): void
    {
        self::$advanceFields = $advanceFields;
    }

    /**
     * @var int
     */
    public static $arrayKeyOfAdvanceFields = 0;

    /**
     * @return int
     */
    public static function getArrayKeyOfAdvanceFields(): int
    {
        return self::$arrayKeyOfAdvanceFields;
    }

    /**
     * @param int $arrayKeyOfAdvanceFields
     */
    public static function setArrayKeyOfAdvanceFields(int $arrayKeyOfAdvanceFields): void
    {
        self::$arrayKeyOfAdvanceFields = $arrayKeyOfAdvanceFields;
    }
}
