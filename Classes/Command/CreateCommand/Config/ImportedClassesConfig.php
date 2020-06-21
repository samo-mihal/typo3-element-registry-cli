<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\AbstractSetup;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ImportedClasses
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config
 */
class ImportedClassesConfig
{
    /**
     * @return array
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getClasses(): array
    {
        $mainExtension = AbstractSetup::getMainExtensionInNameSpaceFormat();
        $vendor = AbstractSetup::getVendor();

        $createCommandCustomData = GeneralUtility::makeInstance($vendor . "\\" . $mainExtension . "\\CreateCommandConfig\CreateCommandCustomData");
        $newConfiguredTraits = $createCommandCustomData->traitsAndClasses();
        $defaultClasses = [
            'objectStorage' => 'TYPO3\CMS\Extbase\Persistence\ObjectStorage',
            'fileReference' => 'TYPO3\CMS\Core\Resource\FileReference'
        ];

        return $newConfiguredTraits ? array_merge($newConfiguredTraits, $defaultClasses) : $defaultClasses;
    }
}
