<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\Fields;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FieldRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\Fields
 */
class FieldRender
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
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function fieldToTca(FieldObject $field): string
    {
        $fieldConfig = GeneralUtility::makeInstance(FieldConfigRender::class, $this->render);
        $table = $this->render->getTable();
        $extensionName = $this->render->getExtensionName();
        $fieldNameInTca = $this->fieldNameInTca($field);
        $tcaFieldLabel = $field->getTitle() ?
            '    \'label\' => \'LLL:EXT:' . $extensionName . '/Resources/Private/Language/locallang_db.xlf:' . $table . '.' . $fieldNameInTca . '\',' :
            null;

        $template[] = '\'' . $fieldNameInTca . '\' => [';
        if ($tcaFieldLabel) {
            $template[] = $tcaFieldLabel;
        }
        $template[] = '    \'config\' => ' . $fieldConfig->getConfig($field)[$field->getType()];
        $template[] = '],';

        return implode("\n" . $this->render->getFields()->getSpacesInTcaColumn(), $template);
    }

    /**
     * @param FieldObject $field
     * @return string
     */
    public function fieldNameInTca(FieldObject $field): string
    {
        $fieldName = $field->getName();

        return $this->render->isTcaFieldsPrefix() ?
            strtolower($this->render->getName()) . '_' . $fieldName :
            $fieldName;
    }
}
