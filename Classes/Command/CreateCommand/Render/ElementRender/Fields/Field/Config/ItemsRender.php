<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\Fields\Field\Config;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\AbstractRender;
use InvalidArgumentException;

/**
 * Class ItemsRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\Element\Field\Config
 */
class ItemsRender extends AbstractRender
{
    /**
     * @var FieldObject
     */
    protected $field = null;

    /**
     * TCA constructor.
     * @param ElementRender $elementRender
     * @param FieldObject $field
     */
    public function __construct(ElementRender $elementRender, FieldObject $field)
    {
        parent::__construct($elementRender);
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
                    $translationId = $item->getNameInTranslation($this->elementRender, $field);

                    $result[] =
                        ElementObject::FIELDS_TAB . '[\'' . $this->elementRender->getElement()->getTranslationPathShort() . ':' . $translationId . '\', ' . $item->getConstantPath($this->elementRender, $field) . '],';
                    $this->elementRender->translation()->addStringToTranslation(
                        $this->elementRender->getElement()->getTranslationPathFromRoot(),
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

        return implode("\n" . $this->elementRender->getElement()->getFieldsSpacesInTcaColumnConfigItems(), $result);
    }
}
