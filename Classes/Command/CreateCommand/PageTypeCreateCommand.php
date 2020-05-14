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
 * Class PageType
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand
 */
class PageTypeCreateCommand extends Command
{
    /**
     * Table for Page types
     */
    const TABLE = 'pages';

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
    public function execute(InputInterface $input, OutputInterface $output)
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
        $fields->setFieldsSpacesInTcaColumnsOverrides('               ');
        $element = GeneralUtility::makeInstance(ElementRender::class);
        $element->setExtensionName($extensionName);
        $element->setElement($fields);
        $element->setInlineRelativePath($relativePathToModel);
        $element->setName($pageTypeName);
        $element->setTable($table);
        $element->setInlineFields($inlineFields);
        $element->setModelNamespace($namespaceToContentElementModel);
        $element->setStaticName($pageTypeName);
        $element->setDoktype($doktype);
        $element->setInput($input);
        $element->setOutput($output);
        $element->setType('PageType');
        $element->setAutoHeader($autoHeader);
        $element->setVendor($vendor);
        $element->setMainExtension($mainExtension);
        $element->setBetweenProtectedsAndGetters(
            implode(
                "\n",
                [
                    '    /**',
                    '     * @var int',
                    '     */',
                    '    protected static $doktype = ' . $doktype . ';' . "\n"
                ]
            )
        );

        $element->check()->pageTypeCreateCommand();
        $element->icon()->copyPageTypeDefaultIcon();
        $element->model()->pageTypeTemplate();
        $element->tca()->pageTypeTemplate();
        $element->typoScript()->pageTypeTypoScriptRegister();
        $element->template()->pageTypeTemplate();
        $element->translation()->addFieldsTitleToTranslation(
            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf'
        );
        $element->translation()->addStringToTranslation(
            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf',
            'page.type.'. $doktype . '.label',
            $pageTypeTitle
        );
        $element->register()->pageTypeToExtTables();
        $element->sqlDatabase()->defaultFields();
        $element->inline()->render();

        $output->writeln('<bg=red;options=bold>• Change PageType Icon.</>');
        $element->typo3Cms()->compareDatabase();
        $element->typo3Cms()->fixFileStructure();
        $element->typo3Cms()->clearCache();
        $output->writeln('<bg=green;options=bold>Page type ' . $pageTypeName . ' was created.</>');
    }
}
