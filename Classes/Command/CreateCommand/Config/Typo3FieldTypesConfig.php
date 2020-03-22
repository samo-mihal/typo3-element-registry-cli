<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\SQLDatabaseRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use Digitalwerk\Typo3ElementRegistryCli\Command\RunCreateElementCommand;
use Digitalwerk\Typo3ElementRegistryCli\Utility\TranslationUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Typo3FieldTypes
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config
 */
class Typo3FieldTypesConfig
{
    /**
     * @param string $table
     * @return array
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getTCAFieldTypes($table): array
    {
        return [
            $table => array_merge($this->getTypo3NewCustomFieldTypes(),$this->getDefaultTCAFieldTypes($table)),
        ];
    }


    /**
     * @param $table
     * @return array
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getDefaultTCAFieldTypes($table)
    {
        $defaultFieldTypes = $GLOBALS['TCA'][$table]['columns'];
        $result = [];

        foreach (array_keys($defaultFieldTypes) as $defaultFieldType) {
            if (!in_array($defaultFieldType, $result)) {
                $defaultFieldTypeTitle = $defaultFieldTypes[$defaultFieldType]['label'];
                if ($defaultFieldTypeTitle) {
                    $file = 'public/typo3conf/ext/' . explode(':', $defaultFieldTypeTitle)[2];
                    $file = file_exists($file) ? $file : 'public/typo3/sysext/' . explode(':', $defaultFieldTypeTitle)[2];
                    if (file_exists($file)) {
                        $defaultFieldTypeTitle = TranslationUtility::getSourceByFileNameAndId($file, explode(':', $defaultFieldTypeTitle)[3]);

                        $result[$defaultFieldType] = [
                            'isFieldDefault' => true,
                            'defaultFieldTitle' => $defaultFieldTypeTitle,
                            'tableFieldDataType' => null,
                            'config' => null,
                        ];

                        $result[$defaultFieldType]['TCAItemsAllowed'] = $defaultFieldTypes[$defaultFieldType]['config']['items'] ? true : false;

                        if ($defaultFieldTypes[$defaultFieldType]['config']['type'] === 'inline') {
//                            Default model property for inline
                            $result[$defaultFieldType]['importClass'][] = 'objectStorage';
                            if ($defaultFieldTypes[$defaultFieldType]['config']['foreign_table_field'] !== 'tablenames') {
                                $result[$defaultFieldType]['inlineFieldsAllowed'] = true;
                            }
                        } elseif ($defaultFieldTypes[$defaultFieldType]['config']['type'] === 'group') {
//                            Default model property for group
                            $result[$defaultFieldType]['importClass'][] = 'objectStorage';
                        } elseif ($defaultFieldTypes[$defaultFieldType]['config']['type'] === 'flex') {
//                            Default model property for flex
                            $result[$defaultFieldType]['FlexFormItemsAllowed'] = true;
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @return array
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getTypo3NewCustomFieldTypes()
    {
        $sqlDatabase = GeneralUtility::makeInstance(SQLDatabaseRender::class, new RenderCreateCommand());
        $mainExtension = GeneralUtility::makeInstance(RunCreateElementCommand::class)->getMainExtensionInNameSpaceFormat();
        $vendor = GeneralUtility::makeInstance(RunCreateElementCommand::class)->getVendor();

        $createCommandCustomData = GeneralUtility::makeInstance($vendor . "\\" . $mainExtension . "\\CreateCommandConfig\CreateCommandCustomData");
        $newConfiguredFields = $createCommandCustomData->typo3TcaFieldTypes();

        $defaultConfiguredFields = [
            'input' => [
                'isFieldDefault' => false,
                'tableFieldDataType' => $sqlDatabase->getVarchar255DataType(),
                'TCAItemsAllowed' => false,
            ],
            'select' => [
                'isFieldDefault' => false,
                'tableFieldDataType' => $sqlDatabase->getIntDataType(),
                'TCAItemsAllowed' => true,
            ],
            'fal' => [
                'isFieldDefault' => false,
                'tableFieldDataType' => $sqlDatabase->getIntDataType(),
                'TCAItemsAllowed' => false,
                'importClass' => [
                    'objectStorage',
                ],
            ],
            'radio' => [
                'isFieldDefault' => false,
                'tableFieldDataType' => $sqlDatabase->getIntDataType(),
                'TCAItemsAllowed' => true,
            ],
            'textarea' => [
                'isFieldDefault' => false,
                'tableFieldDataType' => $sqlDatabase->getTextDataType(),
                'TCAItemsAllowed' => false,
            ],
            'check' => [
                'isFieldDefault' => false,
                'tableFieldDataType' => $sqlDatabase->getIntDataType(),
                'TCAItemsAllowed' => true,
            ],
            'group' => [
                'isFieldDefault' => false,
                'tableFieldDataType' => $sqlDatabase->getVarchar255DataType(),
                'TCAItemsAllowed' => false,
                'importClass' => [
                    'objectStorage',
                ],
            ],
            'inline' => [
                'isFieldDefault' => false,
                'defaultFieldTitle' => null,
                'tableFieldDataType' => $sqlDatabase->getIntDataType(),
                'TCAItemsAllowed' => false,
                'FlexFormItemsAllowed' => false,
                'importClass' => [
                    'objectStorage',
                ],
                'inlineFieldsAllowed' => true
            ],
        ];
        return $newConfiguredFields ? array_merge($newConfiguredFields, $defaultConfiguredFields) : $defaultConfiguredFields;
    }
}
