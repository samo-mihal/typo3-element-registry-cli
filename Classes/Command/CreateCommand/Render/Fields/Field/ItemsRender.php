<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\Fields\Field;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use InvalidArgumentException;

/**
 * Class ItemsRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\Fields\Field
 */
class ItemsRender
{
    /**
     * @var null
     */
    protected $render = null;

    /**
     * TCA constructor.
     * @param RenderCreateCommand $render
     */
    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
    }

    /**
     * @param FieldObject $field
     * @param $spaceFromLeft
     * @return string
     */
    public function itemsToTcaFromField(FieldObject $field, $spaceFromLeft)
    {
        $result = [];
        $spaceFromLeft = $spaceFromLeft . '    ';
        $extensionName = $this->render->getExtensionName();
        $table = $this->render->getTable();
        $relativePath = $this->render->getModelNamespace();
        $name = $this->render->getStaticName();
        $secondDesignation = $this->render->getName();
        $fieldName = $field->getName();
        $fieldType = $field->getType();
        $items = $field->getItems();


        if ($field->hasItems() && !$field->isFlexFormItemsAllowed()) {
            if ($field->isTCAItemsAllowed()) {
                foreach ($items as $item) {
                    $itemName = $item->getName();
                    $translationId = $table . '.' . str_replace('_', '', $extensionName) . '_'.strtolower($name).'.'. strtolower($secondDesignation).'_'.$fieldName.'.' . strtolower($itemName);

                    $result[] = '[\'LLL:EXT:' . $extensionName . '/Resources/Private/Language/locallang_db.xlf:' . $translationId . '\', ' . '\\' . $relativePath . '\\' . $secondDesignation . '::' . strtoupper($fieldName) . '_' .strtoupper($itemName) . '],';
                    $this->render->translation()->addStringToTranslation(
                        'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf',
                        $translationId,
                        $item->getTitle()
                    );
                }
            } else {
                throw new InvalidArgumentException('You can not add items to ' . $fieldType . ', because items is not allowed.');
            }
        }

        return implode("\n" . $spaceFromLeft, $result);
    }
}
