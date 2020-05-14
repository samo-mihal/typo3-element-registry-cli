<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use DOMDocument;
use SimpleXMLElement;

/**
 * Class TranslationRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
class TranslationRender extends AbstractRender
{
    /**
     * TranslationRender constructor.
     * @param ElementRender $elementRender
     */
    public function __construct(ElementRender $elementRender)
    {
        parent::__construct($elementRender);
    }

    /**
     * @param $file
     * @param $translationId
     * @param $translationValue
     */
    public function addStringToTranslation($file, $translationId, $translationValue)
    {
        $xml = simplexml_load_file($file);
        $body = $xml->file->body;

        $transUnit = $body->addChild('trans-unit');
        $transUnit->addAttribute('id',$translationId);
        $transUnit->addChild('source', ''.str_replace('-',' ',$translationValue).'');

        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        $formatXml = new SimpleXMLElement($dom->saveXML());
        $formatXml->saveXML($file);
    }

    /**
     * @param $file
     */
    public function addFieldsTitleToTranslation($file)
    {
        $fields = $this->elementRender->getElement()->getFields();

        if ($fields) {
            $xml = simplexml_load_file($file);
            $body = $xml->file->body;

            /** @var FieldObject $field */
            foreach ($fields as $field) {
                $fieldTitle = $field->getTitle();

                if ($fieldTitle !== $field->getDefaultTitle() && !empty($fieldTitle))
                {
                    $transUnitField = $body->addChild('trans-unit');
                    $transUnitField->addAttribute('id', $field->getNameInTranslation($this->elementRender));
                    $transUnitField->addChild('source', $fieldTitle);
                }
            }

            $dom = new DOMDocument('1.0');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($xml->asXML());
            $formatXml = new SimpleXMLElement($dom->saveXML());
            $formatXml->saveXML($file);
        }
    }
}
