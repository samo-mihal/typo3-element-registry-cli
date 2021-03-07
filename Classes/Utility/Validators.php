<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Utility;

/**
 * Class Validators
 * @package Digitalwerk\Typo3ElementRegistryCli\Utility
 */
class Validators
{
    /**
     * @param $value
     */
    public static function notEmpty($value)
    {
        if (empty($value)) {
            throw new \RuntimeException('Value cannot be empty');
        }
    }

    /**
     * @param $value
     */
    public static function camelCase($value)
    {
        if (strpos(trim($value), ' ') !== false) {
            throw new \RuntimeException('Value must be camel case format.');
        }
    }
}
