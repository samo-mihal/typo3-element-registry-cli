<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\AbstractSetup;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FlexFormFieldTypes
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config
 */
class FlexFormFieldTypesConfig
{
    /**
     * @return array
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getFlexFormFieldTypes(): array
    {
        $mainExtension = AbstractSetup::getMainExtensionInNameSpaceFormat();
        $vendor = AbstractSetup::getVendor();

        $createCommandCustomData = GeneralUtility::makeInstance($vendor . "\\" . $mainExtension . "\\CreateCommandConfig\CreateCommandCustomData");
        $newConfiguredFields = $createCommandCustomData->typo3FlexFormFieldTypes();

        $defaultConfiguredFields = [
            'input' => [
                'config' => $this->getInputConfig()
            ],
            'group' => [
                'config' => $this->getGroupConfig()
            ],
            'textarea' => [
                'config' => $this->getTextAreaConfig()
            ],
            'link' => [
                'config' => $this->getLinkConfig()
            ],
            'select' => [
                'config' => $this->getSelectConfig()
            ],
            'section' => [
                'config' => $this->getSectionConfig()
            ],
            'check' => [
                'config' => $this->getCheckConfig()
            ],
        ];

        return $newConfiguredFields ? array_merge($newConfiguredFields, $defaultConfiguredFields) : $defaultConfiguredFields;
    }

    /**
     * @return string
     */
    public function getInputConfig(): string
    {
        return '<type>input</type>
                                <max>255</max>
                                <eval>trim</eval>';
    }

    /**
     * @return string
     */
    public function getGroupConfig(): string
    {
        return '<type>group</type>
                                <internal_type>db</internal_type>
                                <allowed>pages</allowed>
                                <suggestOptions>
                                    <pages>
                                        <searchCondition>doktype = 99</searchCondition>
                                    </pages>
                                </suggestOptions>';
    }

    /**
     * @return string
     */
    public function getTextAreaConfig(): string
    {
        return '<type>text</type>';
    }

    /**
     * @return string
     */
    public function getLinkConfig(): string
    {
        return '<type>link</type>
                                <renderType>inputLink</renderType>';
    }

    /**
     * @return string
     */
    public function getSelectConfig(): string
    {
        return '<type>select</type>
                                <renderType>selectSingle</renderType>
                                <items>
                                    <numIndex index="0">
                                        <numIndex index="0">3</numIndex>
                                        <numIndex index="1">3</numIndex>
                                    </numIndex>
                                    <numIndex index="1">
                                        <numIndex index="0">6</numIndex>
                                        <numIndex index="1">6</numIndex>
                                    </numIndex>
                                </items>';
    }

    /**
     * @return string
     */
    public function getSectionConfig(): string
    {
        return '        NeedToEdit
                        <el>
                            <topic>
                                <type>array</type>
                                <title>Change</title>
                                <el>
                                    <name>
                                        <TCEforms>
                                            <label>Change</label>
                                            <config>
                                                <type>input</type>
                                                <eval>trim</eval>
                                                <max>255</max>
                                            </config>
                                        </TCEforms>
                                    </name>
                                </el>
                            </topic>
                        </el>';
    }

    /**
     * @return string
     */
    public function getCheckConfig(): string
    {
        return '<type>check</type>';
    }
}
