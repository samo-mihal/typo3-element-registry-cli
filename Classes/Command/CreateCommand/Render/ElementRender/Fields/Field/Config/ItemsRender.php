<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\Fields\Field\Config;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\FieldsObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\AbstractRender;
use InvalidArgumentException;

/**
 * Class ItemsRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\Fields\Field\Config
 */
class ItemsRender extends AbstractRender
{
    /**
     * @var FieldObject
     */
    protected $field = null;

    /**
     * TCA constructor.
     * @param ElementRender $element
     * @param FieldObject $field
     */
    public function __construct(ElementRender $element, FieldObject $field)
    {
        parent::__construct($element);
        $this->field = $field;
    }

    /**
     * @param FieldObject $field
     * @return string
     */
    public function itemsToTcaFromField(FieldObject $field)
    {
        $result = [];
        $fieldType = $field->getType();
        $items = $field->getItems();

        if ($field->hasItems() && !$field->isFlexFormItemsAllowed()) {
            if ($field->isTCAItemsAllowed()) {
                foreach ($items as $item) {
                    $translationId = $item->getNameInTranslation($this->element, $field);

                    $result[] =
                        FieldsObject::TAB . '[\'' . $this->element->getTranslationPathShort() . ':' . $translationId . '\', ' . $item->getConstantPath($this->element, $field) . '],';
                    $this->element->translation()->addStringToTranslation(
                        $this->element->getTranslationPathFromRoot(),
                        $translationId,
                        $item->getTitle()
                    );
                }
            } else {
                throw new InvalidArgumentException(
                    'You can not add items to ' . $fieldType . ', because items is not allowed.'
                );
            }
        }

        return implode("\n" . $this->element->getFields()->getSpacesInTcaColumnConfigItems(), $result);
    }
}
