<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config;

/**
 * Class FlexFormFieldTypes
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config
 */
class FlexFormFieldTypesConfig
{
    /**
     * @return array
     */
    public function getFlexFormFieldTypes(): array
    {
        $inputConfig = $this->getInputConfig();
        $groupConfig = $this->getGroupConfig();
        $textareaConfig = $this->getTextAreaConfig();
        $linkConfig = $this->getLinkConfig();

        return [
            'input' => [
                'config' => $inputConfig
            ],
            'group' => [
                'config' => $groupConfig
            ],
            'textarea' => [
                'config' => $textareaConfig
            ],
            'link' => [
                'config' => $linkConfig
            ],
        ];
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
}
