<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\SQLDatabaseRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\AbstractSetup;
use Digitalwerk\Typo3ElementRegistryCli\Utility\TranslationUtility;
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
            $table => array_merge($this->getTypo3NewCustomFieldTypes(), $this->getDefaultTCAFieldTypes($table)),
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
            if (
                $defaultFieldTypes[$defaultFieldType]['config']['type'] === 'inline' ||
                $defaultFieldTypes[$defaultFieldType]['config']['type'] === 'group' ||
                $defaultFieldTypes[$defaultFieldType]['config']['type'] === 'select' ||
                $defaultFieldTypes[$defaultFieldType]['config']['type'] === 'radio' ||
                $defaultFieldTypes[$defaultFieldType]['config']['type'] === 'check' ||
                $defaultFieldTypes[$defaultFieldType]['config']['type'] === 'input' ||
                $defaultFieldTypes[$defaultFieldType]['config']['type'] === 'text' ||
                $defaultFieldTypes[$defaultFieldType]['config']['type'] === 'flex'
            ) {
                if (!in_array($defaultFieldType, $result)) {
                    $defaultFieldTypeTitle = $defaultFieldTypes[$defaultFieldType]['label'];
                    if ($defaultFieldTypeTitle) {
                        $file = 'public/typo3conf/ext/' . explode(':', $defaultFieldTypeTitle)[2];
                        $file = file_exists($file) ? $file : 'public/typo3/sysext/' . explode(':', $defaultFieldTypeTitle)[2];
                        if (file_exists($file)) {
                            $defaultFieldTypeTitle = TranslationUtility::getSourceByFileNameAndId($file, explode(':', $defaultFieldTypeTitle)[3]);
                        }
                    }
                    $result[$defaultFieldType] = [
                        'isFieldDefault' => true,
                        'defaultFieldTitle' => $defaultFieldTypeTitle ?: null,
                        'tableFieldDataType' => null,
                    ];

                    if ($defaultFieldTypes[$defaultFieldType]['config']['type'] === 'inline') {
                        if ($defaultFieldTypes[$defaultFieldType]['config']['maxitems'] === 1) {
                            $result[$defaultFieldType]['importClass'][] = 'fileReference';
                        } else {
                            $result[$defaultFieldType]['importClass'][] = 'objectStorage';
                        }
                        if ($defaultFieldTypes[$defaultFieldType]['config']['foreign_table_field'] !== 'tablenames') {
                            $result[$defaultFieldType]['importClass'][] = 'objectStorage';
                            $result[$defaultFieldType]['inlineFieldsAllowed'] = true;
                        }
                    } elseif ($defaultFieldTypes[$defaultFieldType]['config']['type'] === 'group') {
                        $result[$defaultFieldType]['importClass'][] = 'objectStorage';
                    } elseif ($defaultFieldTypes[$defaultFieldType]['config']['type'] === 'select') {
                        $result[$defaultFieldType]['TCAItemsAllowed'] = true;
                    } elseif ($defaultFieldTypes[$defaultFieldType]['config']['type'] === 'radio') {
                        $result[$defaultFieldType]['TCAItemsAllowed'] = true;
                    } elseif ($defaultFieldTypes[$defaultFieldType]['config']['type'] === 'check') {
                        $result[$defaultFieldType]['TCAItemsAllowed'] = true;
                    } elseif ($defaultFieldTypes[$defaultFieldType]['config']['type'] === 'flex') {
                        $result[$defaultFieldType]['FlexFormItemsAllowed'] = true;
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
        $mainExtension = AbstractSetup::getMainExtensionInNameSpaceFormat();
        $vendor = AbstractSetup::getVendor();

        $createCommandCustomData = GeneralUtility::makeInstance($vendor . "\\" . $mainExtension . "\\CreateCommandConfig\CreateCommandCustomData");
        $newConfiguredFields = $createCommandCustomData->typo3TcaFieldTypes();

        $defaultConfiguredFields = [
            'input' => [
                'isFieldDefault' => false,
                'tableFieldDataType' => SQLDatabaseRender::VARCHAR_255,
                'TCAItemsAllowed' => false,
            ],
            'select' => [
                'isFieldDefault' => false,
                'tableFieldDataType' => SQLDatabaseRender::INT_11,
                'TCAItemsAllowed' => true,
            ],
            'fal' => [
                'isFieldDefault' => false,
                'tableFieldDataType' => SQLDatabaseRender::INT_11,
                'TCAItemsAllowed' => false,
                'importClass' => [
                    'objectStorage',
                ],
            ],
            'radio' => [
                'isFieldDefault' => false,
                'tableFieldDataType' => SQLDatabaseRender::INT_11,
                'TCAItemsAllowed' => true,
            ],
            'textarea' => [
                'isFieldDefault' => false,
                'tableFieldDataType' => SQLDatabaseRender::TEXT,
                'TCAItemsAllowed' => false,
            ],
            'check' => [
                'isFieldDefault' => false,
                'tableFieldDataType' => SQLDatabaseRender::INT_11,
                'TCAItemsAllowed' => true,
            ],
            'group' => [
                'isFieldDefault' => false,
                'tableFieldDataType' => SQLDatabaseRender::VARCHAR_255,
                'TCAItemsAllowed' => false,
                'importClass' => [
                    'objectStorage',
                ],
            ],
            'inline' => [
                'isFieldDefault' => false,
                'defaultFieldTitle' => null,
                'tableFieldDataType' => SQLDatabaseRender::INT_11,
                'TCAItemsAllowed' => false,
                'FlexFormItemsAllowed' => false,
                'importClass' => [
                    'objectStorage',
                ],
                'inlineFieldsAllowed' => true
            ],
            'pass_through' => [
                'isFieldDefault' => false,
                'defaultFieldTitle' => null,
                'tableFieldDataType' => SQLDatabaseRender::INT_11,
                'hasModel' => false,
            ],
        ];
        return $newConfiguredFields ? array_merge($newConfiguredFields, $defaultConfiguredFields) : $defaultConfiguredFields;
    }
}
