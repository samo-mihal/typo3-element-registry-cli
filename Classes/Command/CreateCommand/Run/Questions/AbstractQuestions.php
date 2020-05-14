<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Run\Questions;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Run\ValidatorsRun;
use Digitalwerk\Typo3ElementRegistryCli\Command\RunCreateElementCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * Class AbstractQuestions
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Run\Questions
 */
abstract class AbstractQuestions
{
    const YES_SHORTCUT = 'y';
    const NO_SHORTCUT = 'n';
    const YES = 'Yes';
    const NO = 'No';

    /**
     * @var RunCreateElementCommand
     */
    protected $run = null;

    /**
     * @var InputInterface
     */
    protected $input = null;

    /**
     * @var OutputInterface
     */
    protected $output = null;

    /**
     * @var null
     */
    protected $elementObject = null;

    /**
     * @var ValidatorsRun
     */
    protected $validators = null;

    /**
     * QuestionsRun constructor.
     * @param RunCreateElementCommand $run
     */
    public function __construct(RunCreateElementCommand $run)
    {
        $this->run = $run;
        $this->elementObject = $this->run->getElementObject();
        $this->input = $this->run->getInput();
        $this->output = $this->run->getOutput();
        $this->validators = $run->getValidators();
    }

    /**
     * @return bool
     */
    public function needCreateFields()
    {
        $question = new ChoiceQuestion(
            'Do you want to create some fields?',
            [self::YES_SHORTCUT => self::YES, self::NO_SHORTCUT => self::NO]
        );

        return $this->run->getQuestionHelper()
                ->ask($this->run->getInput(), $this->run->getOutput(), $question) === self::YES_SHORTCUT;
    }
}
