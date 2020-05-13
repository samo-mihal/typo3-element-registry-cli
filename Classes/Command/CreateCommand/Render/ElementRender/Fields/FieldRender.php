<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\Fields;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\AbstractRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\Fields\Field\ConfigRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\Fields\Field\DataDescriptionRender;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FieldRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\Fields
 */
class FieldRender extends AbstractRender
{
    /**
     * @var ConfigRender
     */
    protected $fieldConfigRender = null;

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
        $this->fieldConfigRender = GeneralUtility::makeInstance(ConfigRender::class, $element, $field);
    }

    /**
     * @return string
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function fieldToTca(): string
    {
        $fieldNameInTca = $this->field->getNameInTCA($this->element);
        $tcaFieldLabel = $this->field->getTitle() ? '    ' . $this->fieldLabelInTca() : null;

        $template[] = '\'' . $fieldNameInTca . '\' => [';
        if ($tcaFieldLabel) {
            $template[] = $tcaFieldLabel;
        }
        $template[] = '    \'config\' => ' . $this->fieldConfigRender->getConfig()[$this->field->getType()];
        $template[] = '],';

        return implode("\n" . $this->element->getFields()->getSpacesInTcaColumn(), $template);
    }

    /**
     * @return string
     */
    public function fieldToTcaColumnsOverrides(): string
    {
        $fieldNameInTca = $this->field->getNameInTCA($this->element);
        $tcaFieldLabel = $this->field->getTitle() ? '    ' . $this->fieldLabelInTca() : null;

        $template[] = '\'' . $fieldNameInTca . '\' => [';
        if ($tcaFieldLabel) {
            $template[] = $tcaFieldLabel;
        }
        if ($this->field->isInlineItemsAllowed() && $this->element->getExtensionName() === $this->element->getMainExtension()) {
            $template[] = '    \'config\' => ' . $this->fieldConfigRender->getInlineConfig(
                $this->field,
                $this->element->getFields()->getSpacesInTcaColumnsOverridesConfig()
                );
        }
        $template[] = '],';

        return implode("\n" . $this->element->getFields()->getSpacesInTcaColumnsOverrides(), $template);
    }

    /**
     * @return FieldObject
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function fillFieldDescription(): FieldObject
    {
        return GeneralUtility::makeInstance(DataDescriptionRender::class, $this->element, $this->field)->getDescription();
    }

    /**
     * @return string
     */
    public function fieldLabelInTca(): string
    {
        return '\'label\' => \'' . $this->element->getTranslationPathShort() . ':' . $this->field->getNameInTranslation($this->element) . '\',';
    }
}
