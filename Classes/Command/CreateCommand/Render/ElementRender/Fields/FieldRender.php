<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\Fields;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\AbstractRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\Fields\Field\ConfigRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\Fields\Field\DataDescriptionRender;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FieldRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\Element
 */
class FieldRender extends AbstractRender
{
    /**
     * @var ConfigRender
     */
    protected $fieldConfigRender = null;

    /**
     * @var ElementRender\Fields\Field\Config\ItemsRender
     */
    protected $itemsRender = null;

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
        $this->fieldConfigRender = GeneralUtility::makeInstance(ConfigRender::class, $elementRender, $field);
        $this->itemsRender = GeneralUtility::makeInstance(
            ElementRender\Fields\Field\Config\ItemsRender::class,
            $elementRender, $field
        );
    }

    /**
     * @return string
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function fieldToTca(): string
    {
        $fieldNameInTca = $this->field->getNameInTCA($this->elementRender->getElement());
        $tcaFieldLabel = $this->field->getTitle() ? '    ' . $this->fieldLabelInTca() : null;

        $template[] = '\'' . $fieldNameInTca . '\' => [';
        if ($tcaFieldLabel) {
            $template[] = $tcaFieldLabel;
        }
        $template[] = '    \'config\' => ' . $this->fieldConfigRender->getConfig()[$this->field->getType()];
        $template[] = '],';

        return implode("\n" . $this->elementRender->getElement()->getFieldsSpacesInTcaColumn(), $template);
    }

    /**
     * @return string
     */
    public function fieldToTcaColumnsOverrides(): string
    {
        $fieldNameInTca = $this->field->getNameInTCA($this->elementRender->getElement());
        $tcaFieldLabel = $this->field->getTitle() ? ElementObject::FIELDS_TAB . $this->fieldLabelInTca() : null;

        $template[] = '\'' . $fieldNameInTca . '\' => [';
        if ($tcaFieldLabel) {
            $template[] = $tcaFieldLabel;
        }
        if ($this->field->hasItems()) {
            if ($this->field->isInlineItemsAllowed()) {
                $template[] = ElementObject::FIELDS_TAB . '\'config\' => ' . $this->fieldConfigRender->getInlineConfig(
                        $this->field,
                        $this->elementRender->getElement()->getFieldsSpacesInTcaColumnsOverridesConfig()
                    );
            }
            if ($this->field->isTCAItemsAllowed()) {
                $template[] = ElementObject::FIELDS_TAB . '\'config\' => [';
                $template[] = ElementObject::FIELDS_TAB . ElementObject::FIELDS_TAB . '\'items\' => [';
                $template[] = ElementObject::FIELDS_TAB . ElementObject::FIELDS_TAB .
                    $this->itemsRender->itemsToTcaFromField($this->field);
                $template[] = ElementObject::FIELDS_TAB . ElementObject::FIELDS_TAB . '],';
                $template[] = ElementObject::FIELDS_TAB . '],';
            }
        }
        $template[] = '],';

        return implode("\n" . $this->elementRender->getElement()->getFieldsSpacesInTcaColumnsOverrides(), $template);
    }

    /**
     * @return FieldObject
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function fillFieldDescription(): FieldObject
    {
        return GeneralUtility::makeInstance(DataDescriptionRender::class, $this->elementRender, $this->field)->getDescription();
    }

    /**
     * @return string
     */
    public function fieldLabelInTca(): string
    {
        return '\'label\' => \'' . $this->elementRender->getElement()->getTranslationPathShort() . ':' .
            $this->field->getNameInTranslation($this->elementRender->getElement()) . '\',';
    }
}