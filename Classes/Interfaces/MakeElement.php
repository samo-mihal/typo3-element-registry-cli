<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Interfaces;

use Digitalwerk\Typo3ElementRegistryCli\Command\AbstractMakeCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Output\Output;

/**
 * Interface MakeCommand
 * @package Digitalwerk\Typo3ElementRegistryCli\Interfaces
 */
interface MakeElement
{
    /**
     * MakeElement constructor.
     * @param Input $input
     * @param Output $output
     * @param QuestionHelper $questionHelper
     * @param AbstractMakeCommand $makeCommand
     */
    public function __construct(
        Input $input,
        Output $output,
        QuestionHelper $questionHelper,
        AbstractMakeCommand $makeCommand
    );

    /**
     * @return void
     */
    public function questions(): void;
}
