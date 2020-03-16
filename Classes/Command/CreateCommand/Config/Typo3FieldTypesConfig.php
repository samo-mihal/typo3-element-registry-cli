<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\SQLDatabaseRender;
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
        $importedClasses = GeneralUtility::makeInstance(ImportedClassesConfig::class);
        foreach (array_keys($defaultFieldTypes) as $defaultFieldType) {
            if (!in_array($defaultFieldType, $result)) {
                $defaultFiedTypeTitle = $defaultFieldTypes[$defaultFieldType]['label'];
                if ($defaultFiedTypeTitle) {
                    $file = 'public/typo3conf/ext/' . explode(':', $defaultFiedTypeTitle)[2];
                    $file = file_exists($file) ? $file : 'public/typo3/sysext/' . explode(':', $defaultFiedTypeTitle)[2];
                    if (file_exists($file)) {
                        $defaultFiedTypeTitle = TranslationUtility::getSourceByFileNameAndId($file, explode(':', $defaultFiedTypeTitle)[3]);

                        $result[$defaultFieldType] = [
                            'isFieldDefault' => true,
                            'defaultFieldName' => $defaultFieldType,
                            'defaultFieldTitle' => str_replace(' ', '-', $defaultFiedTypeTitle),
                            'tableFieldDataType' => null,
                            'config' => null,
                        ];

                        $result[$defaultFieldType]['TCAItemsAllowed'] = $defaultFieldTypes[$defaultFieldType]['config']['items'] ? true : false;

                        if ($importedClasses->getClasses()[$defaultFieldType . 'Trait']) {
                            $result[$defaultFieldType]['needImportClass'] = true;
                            $result[$defaultFieldType]['importClassConditional']['needDefaulFieldName'] = true;
                            $result[$defaultFieldType]['importClass'][] = $defaultFieldType . 'Trait';
                            $result[$defaultFieldType]['trait'] = $defaultFieldType . 'Trait';
                        }

                        if ($defaultFieldTypes[$defaultFieldType]['config']['type'] === 'inline') {
//                            Default model property for inline
                            $result[$defaultFieldType]['needImportClass'] = true;
                            $result[$defaultFieldType]['importClassConditional']['needDefaulFieldName'] = false;
                            $result[$defaultFieldType]['importClass'][] = 'objectStorage';
                            if ($defaultFieldTypes[$defaultFieldType]['config']['foreign_table_field'] !== 'tablenames') {
                                $result[$defaultFieldType]['inlineFieldsAllowed'] = true;
                            } else {
                                if ($defaultFieldTypes[$defaultFieldType]['config']['maxitems'] === 1) {
                                    $result[$defaultFieldType]['needImportClass'] = $importedClasses->getClasses()[$defaultFieldType . 'Trait'] ? true : false;
                                }
                            }
                        } elseif ($defaultFieldTypes[$defaultFieldType]['config']['type'] === 'group') {
//                            Default model property for group
                            $result[$defaultFieldType]['needImportClass'] = true;
                            $result[$defaultFieldType]['importClassConditional']['needDefaulFieldName'] = false;
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
        $sqlDatabase = GeneralUtility::makeInstance(SQLDatabaseRender::class, null);
        $mainExtension = GeneralUtility::makeInstance(RunCreateElementCommand::class)->getMainExtensionInNameSpaceFormat();
        $vendor = GeneralUtility::makeInstance(RunCreateElementCommand::class)->getVendor();

        $createCommandCustomData = GeneralUtility::makeInstance($vendor . "\\" . $mainExtension . "\\CreateCommandConfig\CreateCommandCustomData");
        $newConfiguredFields = $createCommandCustomData->typo3FieldTypes();

        $defaultConfiguredFields = [
            'input' => [
                'isFieldDefault' => false,
                'defaultFieldName' => null,
                'tableFieldDataType' => $sqlDatabase->getVarchar255DataType(),
                'TCAItemsAllowed' => false,
                'needImportClass' => false,
                'trait' => null
            ],
            'select' => [
                'isFieldDefault' => false,
                'defaultFieldName' => null,
                'tableFieldDataType' => $sqlDatabase->getIntDataType(),
                'TCAItemsAllowed' => true,
                'needImportClass' => false,
                'trait' => null
            ],
            'fal' => [
                'isFieldDefault' => false,
                'defaultFieldName' => true,
                'tableFieldDataType' => $sqlDatabase->getIntDataType(),
                'TCAItemsAllowed' => false,
                'needImportClass' => true,
                'importClassConditional' => [
                    'needDefaulFieldName' => false
                ],
                'importClass' => [
                    'objectStorage',
                ],
                'trait' => null
            ],
            'radio' => [
                'isFieldDefault' => false,
                'defaultFieldName' => null,
                'tableFieldDataType' => $sqlDatabase->getIntDataType(),
                'TCAItemsAllowed' => true,
                'needImportClass' => false,
                'trait' => null
            ],
            'textarea' => [
                'isFieldDefault' => false,
                'defaultFieldName' => null,
                'tableFieldDataType' => $sqlDatabase->getTextDataType(),
                'TCAItemsAllowed' => false,
                'needImportClass' => false,
                'trait' => null
            ],
            'check' => [
                'isFieldDefault' => false,
                'defaultFieldName' => null,
                'tableFieldDataType' => $sqlDatabase->getIntDataType(),
                'TCAItemsAllowed' => true,
                'needImportClass' => false,
                'trait' => null
            ],
            'group' => [
                'isFieldDefault' => false,
                'defaultFieldName' => null,
                'tableFieldDataType' => $sqlDatabase->getVarchar255DataType(),
                'TCAItemsAllowed' => false,
                'needImportClass' => true,
                'importClassConditional' => [
                    'needDefaulFieldName' => false
                ],
                'importClass' => [
                    'objectStorage',
                ],
                'trait' => null
            ],
            'inline' => [
                'isFieldDefault' => false,
                'defaultFieldName' => null,
                'defaultFieldTitle' => null,
                'tableFieldDataType' => $sqlDatabase->getIntDataType(),
                'TCAItemsAllowed' => false,
                'FlexFormItemsAllowed' => false,
                'needImportClass' => true,
                'importClassConditional' => [
                    'needDefaulFieldName' => false
                ],
                'importClass' => [
                    'objectStorage',
                ],
                'trait' => null,
                'inlineFieldsAllowed' => true
            ],
        ];
        return $newConfiguredFields ? array_merge($newConfiguredFields, $defaultConfiguredFields) : $defaultConfiguredFields;
    }
}
