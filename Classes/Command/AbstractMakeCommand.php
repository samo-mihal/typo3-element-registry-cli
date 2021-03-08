<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command;

use Digitalwerk\Typo3ElementRegistryCli\Interfaces\MakeCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class RunCreateElementCommand
 * @package Digitalwerk\Typo3ElementRegistryCli\Command
 */
abstract class AbstractMakeCommand extends Command implements MakeCommand
{
    /**
     * @var array
     */
    protected $requiredFiles = [];

    /**
     * @var string
     */
    protected $extension = '';

    /**
     * @var string
     */
    protected $table = '';

    /**
     * @var string
     */
    protected $vendor = '';

    /**
     * @var Output
     */
    protected $output = null;

    /**
     * @var Input
     */
    protected $input = null;

    /**
     * @var QuestionHelper
     */
    protected $questionHelper = null;

    /**
     * @var array
     */
    protected $typo3ElementRegistryCliConfig = [];

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->questionHelper = $this->getHelper('question');
        $this->typo3ElementRegistryCliConfig = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('typo3_element_registry_cli');
        $this->vendor = $this->typo3ElementRegistryCliConfig['elementsVendor'];
        $this->beforeMake();
        $this->make();
        $this->afterMake();
    }

    public function beforeMake(): void
    {
        if (empty($this->extension)) {
            throw new \InvalidArgumentException('Extension cannot be empty.');
        }
        if (empty($this->vendor)) {
            throw new \InvalidArgumentException('Vendor cannot be empty.');
        }
        if (empty($this->table)) {
            throw new \InvalidArgumentException('Table cannot be empty.');
        }
        if (ExtensionManagementUtility::isLoaded($this->extension) === false) {
            throw new \InvalidArgumentException('Extension ' . $this->extension . ' is not loaded.');
        }
        if (!empty($this->requiredFiles)) {
            foreach ($this->requiredFiles as $file) {
                $file = str_replace('{extension}', $this->extension, $file);
                $filename = GeneralUtility::getFileAbsFileName($file);
                if (file_exists($filename) === false) {
                    throw new \InvalidArgumentException('Required file doesn\'t exist. File: ' . $filename);
                }
            }
        }
    }

    public function afterMake(): void
    {
        /** Flush Typo3 cache */
        shell_exec('vendor/bin/typo3cms cache:flush');
        $this->output->writeln('<bg=green;options=bold>Flushed all caches.</>');
    }
}
