<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand;

use Digitalwerk\Typo3ElementRegistryCli\Utility\FieldsCreateCommandUtility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class RecordCreateCommand
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand
 */
class RecordCreateCommand extends Command
{

    protected function configure()
    {
        $this->addArgument('vendor', InputArgument::REQUIRED,'Enter vendor of record namespace');
        $this->addArgument('main-extension', InputArgument::REQUIRED,'Enter main extension of record');
        $this->addArgument('extension', InputArgument::REQUIRED,'Enter extension of record');
        $this->addArgument('name', InputArgument::REQUIRED,'Enter name of Record.');
        $this->addArgument('title', InputArgument::REQUIRED,'Enter title of Record.');
        $this->addArgument('fields', InputArgument::REQUIRED,'Enter fields of Record.');
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
        $name = $input->getArgument('name');
        $title = $input->getArgument('title');
        $fields = $input->getArgument('fields');
        $mainExtension = $input->getArgument('main-extension');
        $vendor = $input->getArgument('vendor');
        $extensionName = $input->getArgument('extension');
        $table = 'tx_' . str_replace('_', '', $extensionName) . '_domain_model_' . strtolower($name);
        $relativePathToModel = $extensionName . '/Classes/Domain/Model';
        $extensionNameInNameSpace = str_replace(' ','',ucwords(str_replace('_',' ',$extensionName)));
        $namespaceToModel = $vendor . '\\' . $extensionNameInNameSpace . '\Domain\Model';

        $fields = GeneralUtility::makeInstance(FieldsCreateCommandUtility::class)->generateObject($fields, $table);
        $fields->setSpacesInTcaColumn('        ');
        $render = GeneralUtility::makeInstance(RenderCreateCommand::class);
        $render->setExtensionName($extensionName);
        $render->setTable($table);
        $render->setInlineRelativePath($relativePathToModel);
        $render->setFields($fields);
        $render->setName($name);
        $render->setModelNamespace($namespaceToModel);
        $render->setTcaFieldsPrefix(false);
        $render->setStaticName($name);
        $render->setElementType('Record');
        $render->setOutput($output);
        $render->setInput($input);
        $render->setTitle($title);
        $render->setVendor($vendor);
        $render->setMainExtension($mainExtension);

        $render->check()->recordCreateCommand();
        $render->model()->recordTemplate();
        $render->tca()->recordTemplate();
        $render->icon()->copyRecordDefaultIcon();
        $render->sqlDatabase()->recordFields();
        $render->translation()->addFieldsTitleToTranslation(
            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf'
        );
        $render->translation()->addStringToTranslation(
            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf',
            $table,
            $title
        );

        $output->writeln('<bg=red;options=bold>• Change record Icon.</>');
        $output->writeln('<bg=green;options=bold>Record ' . $name . ' was created.</>');
    }
}
