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
     * @param string $translationId
     * @param string $translationValue
     */
    public function addStringToTranslation(string $translationId, string $translationValue)
    {
        $file = $this->element->getTranslationPath();
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
     * @return void
     */
    public function addFieldsTitleToTranslation(): void
    {
        $fields = $this->fields;
        $filename = $this->element->getTranslationPath();
        if ($fields) {
            $xml = simplexml_load_file($filename);
            $body = $xml->file->body;

            /** @var FieldObject $field */
            foreach ($fields as $field) {
                $fieldTitle = $field->getTitle();

                if ($fieldTitle !== $field->getDefaultTitle() && !empty($fieldTitle))
                {
                    $transUnitField = $body->addChild('trans-unit');
                    $transUnitField->addAttribute('id', $field->getNameInTranslation($this->elementRender->getElement()));
                    $transUnitField->addChild('source', $fieldTitle);
                }
            }

            $dom = new DOMDocument('1.0');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($xml->asXML());
            $formatXml = new SimpleXMLElement($dom->saveXML());
            $formatXml->saveXML($filename);
        }
    }
}
