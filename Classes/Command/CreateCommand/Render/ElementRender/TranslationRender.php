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
     * @var SimpleXMLElement
     */
    protected $xml = null;

    /**
     * @var string
     */
    protected $filename = '';

    /**
     * TranslationRender constructor.
     * @param ElementRender $elementRender
     */
    public function __construct(ElementRender $elementRender)
    {
        parent::__construct($elementRender);
        $this->initialize();
    }

    /**
     * TranslationRender destructor.
     */
    public function __destruct()
    {
        $this->saveXMLFile();
    }

    /**
     * @return void
     */
    private function initialize(): void
    {
        $this->filename = $this->element->getTranslationPath();
        $this->xml = simplexml_load_string(
            str_replace(['<!--', '-->'], ['<comment>', '</comment>'], simplexml_load_file($this->filename)->asXML())
        );
    }

    /**
     * @return void
     */
    private function addElementTypeMarkerToTranslation(): void
    {
        if (empty($this->xml->file->body->xpath('//comment[text()="' . $this->element->getStaticType() . 's' . '"]'))) {
            $this->xml->file->body->addChild(
                'comment',
                $this->element->getStaticType() . 's'
            );
        }

        $this->saveXMLFile();
        $this->initialize();
    }

    /**
     * @return void
     */
    private function addElementNameMarkerToTranslation(): void
    {
        if (empty($this->xml->file->body->xpath('//comment[text()="' . $this->element->getName() . '"]'))) {
            $this->simpleXmlInsertAfter(
                $this->xml->file->body->addChild(
                    'comment',
                    $this->element->getName()
                ),
                $this->xml->xpath('//comment[text()="' . $this->element->getStaticType() . 's' . '"]')[0]
            );
        }
        $this->saveXMLFile();
        $this->initialize();
    }

    /**
     * @return void
     */
    private function addComments(): void
    {
        $this->addElementTypeMarkerToTranslation();
        $this->addElementNameMarkerToTranslation();
    }

    /**
     * @param string $translationId
     * @param string $translationValue
     */
    public function addStringToTranslation(string $translationId, string $translationValue)
    {
        $this->addComments();
        $transUnit = $this->xml->file->body->addChild('trans-unit');
        $transUnit->addAttribute('id', $translationId);
        $transUnit->addChild('source', ''.str_replace('-', ' ', $translationValue).'');
        $this->simpleXmlInsertAfter(
            $transUnit,
            $this->xml->xpath('//comment[text()="' . trim($this->element->getName()) . '"]')[0]
        );
    }

    /**
     * @return void
     */
    public function addFieldsTitleToTranslation(): void
    {
        $fields = $this->fields;
        if ($fields) {
            $this->addComments();
            /** @var FieldObject $field */
            foreach ($fields as $field) {
                $fieldTitle = $field->getTitle();

                if ($fieldTitle !== $field->getDefaultTitle() && !empty($fieldTitle)) {
                    $transUnit = $this->xml->file->body->addChild('trans-unit');
                    $transUnit->addAttribute('id', $field->getNameInTranslation($this->elementRender->getElement()));
                    $transUnit->addChild('source', $fieldTitle);
                    $this->simpleXmlInsertAfter(
                        $transUnit,
                        $this->xml->xpath('//comment[text()="' . $this->element->getName() . '"]')[0]
                    );
                }
            }
        }
    }

    /**
     * @param SimpleXMLElement $insert
     * @param SimpleXMLElement $target
     * @return \DOMNode
     */
    private function simpleXmlInsertAfter(SimpleXMLElement $insert, SimpleXMLElement $target)
    {
        $target_dom = dom_import_simplexml($target);
        $insert_dom = $target_dom->ownerDocument->importNode(dom_import_simplexml($insert), true);
        if ($target_dom->nextSibling) {
            return $target_dom->parentNode->insertBefore($insert_dom, $target_dom->nextSibling);
        } else {
            return $target_dom->parentNode->appendChild($insert_dom);
        }
    }

    /**
     * @return void
     */
    private function saveXMLFile(): void
    {
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML(
            str_replace(['<comment>', '</comment>'], ['<!--', '-->'], $this->xml->asXML())
        );
        $dom->save($this->filename);
    }
}
