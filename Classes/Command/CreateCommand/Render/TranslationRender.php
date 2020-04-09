<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\Fields\FieldRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use DOMDocument;
use SimpleXMLElement;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Translation
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render
 */
class TranslationRender
{
    /**
     * @var RenderCreateCommand
     */
    protected $render = null;

    /**
     * @var FieldRender
     */
    protected $fieldRender = null;

    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
        $this->fieldRender = GeneralUtility::makeInstance(FieldRender::class, $render);
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
        $fields = $this->render->getFields();

        if ($fields) {
            $table = $this->render->getTable();
            $xml = simplexml_load_file($file);
            $body = $xml->file->body;

            /** @var FieldObject $field */
            foreach ($fields->getFields() as $field) {
                $fieldTitle = $field->getTitle();

                if ($fieldTitle !== $field->getDefaultTitle() && !empty($fieldTitle))
                {
                    $transUnitField = $body->addChild('trans-unit');
                    $transUnitField->addAttribute('id',$table . '.' . $this->fieldRender->fieldNameInTranslation($field));
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
