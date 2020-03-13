<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config;

use Digitalwerk\Typo3ElementRegistryCli\Command\RunCreateElementCommand;
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
        $mainExtension = GeneralUtility::makeInstance(RunCreateElementCommand::class)->getMainExtension();
        $mainExtension = str_replace(' ','',ucwords(str_replace('_',' ', $mainExtension)));
        $vendor = GeneralUtility::makeInstance(RunCreateElementCommand::class)->getVendor();

        $createCommandCustomData = GeneralUtility::makeInstance($vendor . "\\" . $mainExtension . "\\CreateCommandConfig\\CreateCommandCustomData");
        $newConfiguredTraits = $createCommandCustomData->traitsAndClasses();
        $defaultClasses = [
            'objectStorage' => 'use TYPO3\CMS\Extbase\Persistence\ObjectStorage;'
        ];

        return $newConfiguredTraits ? array_merge($newConfiguredTraits, $defaultClasses) : $defaultClasses;
    }
}
