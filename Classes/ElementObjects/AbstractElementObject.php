<?php
namespace Digitalwerk\Typo3ElementRegistryCli\ElementObjects;

use Digitalwerk\Typo3ElementRegistryCli\Command\AbstractMakeCommand;
use Digitalwerk\Typo3ElementRegistryCli\Interfaces\MakeElement;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Output\Output;

/**
 * Class AbstractElementObject
 * @package Digitalwerk\Typo3ElementRegistryCli\ElementObjects
 */
abstract class AbstractElementObject implements MakeElement
{
    /**
     * @var Output
     */
    protected $output = null;

    /**
     * @var Input
     */
    protected $input = null;

    /**
     * @var AbstractMakeCommand
     */
    protected $makeCommand = null;

    /**
     * @var QuestionHelper
     */
    protected $questionHelper = null;

    /**
     * AbstractElementObject constructor.
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
    ) {
        $this->input = $input;
        $this->output = $output;
        $this->questionHelper = $questionHelper;
        $this->makeCommand = $makeCommand;
    }
}
