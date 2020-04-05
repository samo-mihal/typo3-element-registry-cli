<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand;

use Digitalwerk\Typo3ElementRegistryCli\Utility\FieldsCreateCommandUtility;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class PageType
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand
 */
class PageTypeCreateCommand extends Command
{

    protected function configure()
    {
        $this->setDescription('Create page type with some fields.');
        $this->addArgument('vendor', InputArgument::REQUIRED,'Enter vendor of page type namespace');
        $this->addArgument('main-extension', InputArgument::REQUIRED,'Enter main extension of page type');
        $this->addArgument('extension', InputArgument::REQUIRED,'Enter extension of page type');
        $this->addArgument('table', InputArgument::REQUIRED,'Enter table of PageType.');
        $this->addArgument('name', InputArgument::REQUIRED,'Enter name of PageType.');
        $this->addArgument('title', InputArgument::REQUIRED,'Enter title of PageType.');
        $this->addArgument('doktype', InputArgument::REQUIRED,'Enter doktype of PageType.');
        $this->addArgument('auto-header', InputArgument::REQUIRED,'Set true, if auto generating header is needed.');
        $this->addArgument('fields', InputArgument::REQUIRED,'Add new fields. format: "fieldName,fieldType,fieldTitle/"');
        $this->addArgument('inline-fields',InputArgument::IS_ARRAY ,'');

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doktype = $input->getArgument('doktype');
        $pageTypeName = $input->getArgument('name');
        $pageTypeTitle = $input->getArgument('title');
        $autoHeader = $input->getArgument('auto-header');
        $fields = $input->getArgument('fields');
        $table = $input->getArgument('table');
        $inlineFields = $input->getArgument('inline-fields');
        $mainExtension = $input->getArgument('main-extension');
        $vendor = $input->getArgument('vendor');
        $extensionName = $input->getArgument('extension');

        $namespaceToContentElementModel = $vendor . '\\' . str_replace(' ','',ucwords(str_replace('_',' ',$extensionName))) . '\Domain\Model';
        $relativePathToModel = $extensionName . '/Classes/Domain/Model';
        $fields = GeneralUtility::makeInstance(FieldsCreateCommandUtility::class)->generateObject($fields, $table);

        $render = GeneralUtility::makeInstance(RenderCreateCommand::class);
        $render->setExtensionName($extensionName);
        $render->setFields($fields);
        $render->setInlineRelativePath($relativePathToModel);
        $render->setName($pageTypeName);
        $render->setTable($table);
        $render->setInlineFields($inlineFields);
        $render->setModelNamespace($namespaceToContentElementModel);
        $render->setStaticName($pageTypeName);
        $render->setDoktype($doktype);
        $render->setInput($input);
        $render->setOutput($output);
        $render->setElementType('PageType');
        $render->setAutoHeader($autoHeader);
        $render->setVendor($vendor);
        $render->setMainExtension($mainExtension);

        $render->check()->pageTypeCreateCommand();
        $render->icon()->copyPageTypeDefaultIcon();
        $render->model()->pageTypeTemplate();
        $render->tca()->pageTypeTemplate();
        $render->typoScript()->pageTypeTypoScriptRegister();
        $render->template()->pageTypeTemplate();
        $render->translation()->addFieldsTitleToTranslation(
            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf'
        );
        $render->translation()->addStringToTranslation(
            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf',
            'page.type.'. $doktype . '.label',
            $pageTypeTitle
        );
        $render->register()->pageTypeToExtTables();
        $render->sqlDatabase()->defaultFields();
        $render->inline()->render();

        $output->writeln('<bg=red;options=bold>• Change PageType Icon.</>');
        $render->typo3Cms()->compareDatabase();
        $render->typo3Cms()->fixFileStructure();
        $render->typo3Cms()->clearCache();
        $output->writeln('<bg=green;options=bold>Page type ' . $pageTypeName . ' was created.</>');
    }
}
