<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\ElementSetup;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RunCreateElementCommand
 * @package Digitalwerk\Typo3ElementRegistryCli\Command
 */
class RunCreateElementCommand extends Command
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Welcome in Typo3 element registry');

        $elementSetup = new ElementSetup($input, $output);
        $elementSetup->setQuestionHelper($this->getHelper('question'));
        $elementSetup->initialize();
    }
}
