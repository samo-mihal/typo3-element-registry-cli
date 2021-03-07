<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Utility;

use DOMDocument;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TranslationUtility
 * @package Digitalwerk\Typo3ElementRegistryCli\Utility
 */
class TranslationUtility
{
    /**
     * @param string $filename
     * @param string $translationId
     * @param string $translationValue
     */
    public static function addStringToTranslation(string $filename, string $translationId, string $translationValue)
    {
        $filename = GeneralUtility::getFileAbsFileName($filename);
        $xml = simplexml_load_file($filename);
        $transUnit = $xml->file->body->addChild('trans-unit');
        $transUnit->addAttribute('id', $translationId);
        $transUnit->addChild('source', $translationValue);

        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML(
            $xml->asXML()
        );
        $dom->save($filename);
    }
}
