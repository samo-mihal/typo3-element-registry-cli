<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
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
        $name = $input->getArgument('name');
        $title = $input->getArgument('title');
        $fields = $input->getArgument('fields');
        $mainExtension = $input->getArgument('main-extension');
        $vendor = $input->getArgument('vendor');
        $extensionName = $input->getArgument('extension');
        $inlineFields = $input->getArgument('inline-fields');
        $table = 'tx_' . str_replace('_', '', $extensionName) . '_domain_model_' . strtolower($name);
        $relativePathToModel = $extensionName . '/Classes/Domain/Model';
        $extensionNameInNameSpace = str_replace(' ','',ucwords(str_replace('_',' ',$extensionName)));
        $namespaceToModel = $vendor . '\\' . $extensionNameInNameSpace . '\Domain\Model';

        $fields = GeneralUtility::makeInstance(FieldsCreateCommandUtility::class)->generateObject($fields, $table);
        $fields->setFieldsSpacesInTcaColumn('        ');
        $element = GeneralUtility::makeInstance(ElementRender::class);
        $element->setExtensionName($extensionName);
        $element->setTable($table);
        $element->setInlineRelativePath($relativePathToModel);
        $element->setElement($fields);
        $element->setName($name);
        $element->setInlineFields($inlineFields);
        $element->setModelNamespace($namespaceToModel);
        $element->setTcaFieldsPrefix(false);
        $element->setStaticName($name);
        $element->setType('Record');
        $element->setOutput($output);
        $element->setInput($input);
        $element->setTitle($title);
        $element->setVendor($vendor);
        $element->setMainExtension($mainExtension);

        $element->check()->recordCreateCommand();
        $element->model()->recordTemplate();
        $element->tca()->recordTemplate();
        $element->icon()->copyRecordDefaultIcon();
        $element->sqlDatabase()->recordFields();
        $element->translation()->addFieldsTitleToTranslation(
            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf'
        );
        $element->translation()->addStringToTranslation(
            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf',
            $table,
            $title
        );
        $element->inline()->render();
        $output->writeln('<bg=red;options=bold>• Change record Icon.</>');
        $output->writeln('<bg=green;options=bold>Record ' . $name . ' was created.</>');
        $element->typo3Cms()->compareDatabase();
        $element->typo3Cms()->fixFileStructure();
        $element->typo3Cms()->clearCache();
    }
}
