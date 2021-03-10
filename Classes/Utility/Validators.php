<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Utility;

use function Symfony\Component\String\u;

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
    public static function integer($value)
    {
        if (is_numeric($value) === false || is_int((int)$value) === false) {
            throw new \RuntimeException('Value must be an integer');
        }
    }

    /**
     * @param $value
     * @param $items
     * @param string $customMessage
     */
    public static function unique($value, $items, string $customMessage = '')
    {
        if (in_array($value, $items)) {
            if (empty($customMessage)) {
                $customMessage = 'Value must unique';
            }
            throw new \RuntimeException($customMessage);
        }
    }

    /**
     * @param $value
     */
    public static function camelCase($value)
    {
        $camel = (string)u($value)->camel()->title(true);

        if ($camel !== (string)$value) {
            throw new \RuntimeException(
                'Value must be (title) camel case format. Example: ' . $camel
            );
        }
    }
}
