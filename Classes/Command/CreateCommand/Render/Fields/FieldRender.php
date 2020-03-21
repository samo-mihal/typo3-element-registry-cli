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
        $fieldName = $field->getName();
        $table = $this->render->getTable();
        $extensionName = $this->render->getExtensionName();
        $name = $this->render->getStaticName();
        $secondDesignation = $this->render->getName();

        return
            '\'' . strtolower($secondDesignation) . '_' . $fieldName . '\' => [
        \'label\' => \'LLL:EXT:' . $extensionName . '/Resources/Private/Language/locallang_db.xlf:' . $table . '.' . str_replace('_','',$extensionName) . '_' . strtolower($name) . '.' . strtolower($secondDesignation) . '_' . $fieldName . '\',
        \'config\' => ' . $fieldConfig->getConfig($field)[$field->getType()] . '
    ],';
    }
}
