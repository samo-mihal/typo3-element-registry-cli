<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Utility;

/**
 * Class TranslationUtility
 * @package Digitalwerk\Typo3ElementRegistryCli\Utility
 */
class TranslationUtility
{
    /**
     * @param $file
     * @param $id
     * @return string
     */
    public static function getSourceByFileNameAndId($file, $id)
    {
        $xml = simplexml_load_file($file);
        if ($xml->file->body) {
            $children = $xml->file->body->children();

            foreach ($children as $child) {
                if ((string) $child->attributes()->id[0] === $id) {
                    return (string) $child->source[0];
                }
            }
        }
    }
}
