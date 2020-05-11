<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\Fields;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FieldRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\Fields
 */
class FieldRender
{
    /**
     * @var RenderCreateCommand
     */
    protected $render = null;

    /**
     * @var FieldConfigRender
     */
    protected $fieldConfigRender = null;

    /**
     * TCA constructor.
     * @param RenderCreateCommand $render
     */
    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
        $this->fieldConfigRender = GeneralUtility::makeInstance(FieldConfigRender::class, $render);
    }

    /**
     * @param FieldObject $field
     * @return string
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function fieldToTca(FieldObject $field): string
    {
        $fieldNameInTca = $this->fieldNameInTca($field);
        $tcaFieldLabel = $field->getTitle() ? '    ' . $this->fieldLabelInTca($field) : null;

        $template[] = '\'' . $fieldNameInTca . '\' => [';
        if ($tcaFieldLabel) {
            $template[] = $tcaFieldLabel;
        }
        $template[] = '    \'config\' => ' . $this->fieldConfigRender->getConfig($field)[$field->getType()];
        $template[] = '],';

        return implode("\n" . $this->render->getFields()->getSpacesInTcaColumn(), $template);
    }

    /**
     * @param FieldObject $field
     * @return string
     */
    public function fieldToTcaColumnsOverrides(FieldObject $field): string
    {
        $fieldNameInTca = $this->fieldNameInTca($field);
        $tcaFieldLabel = $field->getTitle() ? '    ' . $this->fieldLabelInTca($field) : null;

        $template[] = '\'' . $fieldNameInTca . '\' => [';
        if ($tcaFieldLabel) {
            $template[] = $tcaFieldLabel;
        }
        if ($field->isInlineItemsAllowed() && $this->render->getExtensionName() === $this->render->getMainExtension()) {
            $template[] = '    \'config\' => ' . $this->fieldConfigRender->getInlineConfig(
                $field,
                $this->render->getFields()->getSpacesInTcaColumnsOverridesConfig()
                );
        }
        $template[] = '],';

        return implode("\n" . $this->render->getFields()->getSpacesInTcaColumnsOverrides(), $template);
    }

    /**
     * @param FieldObject $field
     * @return string
     */
    public function fieldNameInTca(FieldObject $field): string
    {
        $fieldName = $field->getName();

        if ($field->isDefault()) {
            return $field->getType();
        } elseif ($this->render->isTcaFieldsPrefix() == false) {
            return $fieldName;
        } else {
            return strtolower($this->render->getName()) . '_' . $fieldName;
        }
    }

    /**
     * @param FieldObject $field
     * @return string
     */
    public function fieldNameInModel(FieldObject $field): string
    {
        return str_replace(' ','',lcfirst(ucwords(str_replace('_',' ', $field->getName()))));
    }

    /**
     * @param FieldObject $field
     * @return string
     */
    public function fieldNameInTranslation(FieldObject $field): string
    {
        $fieldName = $field->getName();

        if ($this->render->isTcaFieldsPrefix() == false) {
            return $fieldName;
        } else {
            return strtolower($this->render->getName()) . '_' . $fieldName;
        }
    }

    /**
     * @param FieldObject $field
     * @return FieldObject
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function fillFieldDescription(FieldObject $field): FieldObject
    {
        return GeneralUtility::makeInstance(FieldDataDescriptionRender::class, $this->render)->getDescription($field);
    }

    /**
     * @param FieldObject $field
     * @return string
     */
    public function fieldLabelInTca(FieldObject $field): string
    {
        $table = $this->render->getTable();
        $extensionName = $this->render->getExtensionName();
        return '\'label\' => \'LLL:EXT:' . $extensionName . '/Resources/Private/Language/locallang_db.xlf:' . $table . '.' . $this->fieldNameInTranslation($field) . '\',';
    }
}
