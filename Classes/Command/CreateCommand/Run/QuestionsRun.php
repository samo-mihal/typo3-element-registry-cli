<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Run;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\FlexFormFieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\Typo3FieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\AdvanceFieldsSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields\FlexForm\FlexFormFieldsSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\FieldsSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\RunCreateElementCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class QuestionsRun
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Run
 */
class QuestionsRun
{
    const YES_SHORTCUT = 'y';
    const NO_SHORTCUT = 'n';
    const YES = 'Yes';
    const NO = 'No';
    const DEEP_LEVEL_SPACES = ">>>";

    /**
     * @var RunCreateElementCommand
     */
    protected $run = null;

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
        $this->validators = $run->getValidators();
    }

    /**
     * @var string
     */
    public static $deepLevel = self::DEEP_LEVEL_SPACES;

    /**
     * @return string
     */
    public static function getColoredDeepLevel(): string
    {
        return '<bg=red;options=bold>' . self::getRawDeepLevel() .  '</>';
    }

    /**
     * @return string
     */
    public static function getRawDeepLevel(): string
    {
        return self::$deepLevel;
    }

    public static function setDeepLevelDown(): void
    {
        self::$deepLevel = QuestionsRun::getRawDeepLevel() . QuestionsRun::DEEP_LEVEL_SPACES;
    }

    public static function setDeepLevelUp(): void
    {
        self::$deepLevel = substr(QuestionsRun::getRawDeepLevel(), 0, -strlen(QuestionsRun::DEEP_LEVEL_SPACES));
    }

    /**
     * @param InputInterface $input
     * @return mixed
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function askTCAFields(InputInterface $input)
    {
        if ($this->needCreateFields()) {
            $this->run->setFieldTypes(
                GeneralUtility::makeInstance(Typo3FieldTypesConfig::class)->getTCAFieldTypes($this->run->getTable())[$this->run->getTable()]
            );

            $fieldsSetup = new FieldsSetup($this->run);
            $fieldsSetup->createField();

            $input->setArgument(
                'fields',
                $fieldsSetup->getFields()
            );
            $input->setArgument(
                'inline-fields',
                AdvanceFieldsSetup::getAdvanceFields()
            );

        } else {
            $input->setArgument(
                'fields',
                '-'
            );
        }

        return $input;
    }

    /**
     * @param InputInterface $input
     * @return mixed
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function askFlexFormFields(InputInterface $input)
    {
        if ($this->needCreateFields()) {
            $this->run->setFieldTypes(
                GeneralUtility::makeInstance(FlexFormFieldTypesConfig::class)->getFlexFormFieldTypes()
            );
            $flexFormFields = new FlexFormFieldsSetup($this->run);
            $flexFormFields->createField();

            $input->setArgument(
                'fields',
                $flexFormFields->getFields()
            );
        } else {
            $input->setArgument(
                'fields',
                '-'
            );
        }

        return $input;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function askElementTitle($name)
    {
        $question = new Question(
            $name . ' title (etc. New Element): '
        );
        $this->validators->validateNotEmpty($question);
        return $this->run->getQuestionHelper()->ask(
            $this->run->getInput(),
            $this->run->getOutput(),
            $question
        );
    }

    /**
     * @return mixed
     */
    public function askExtensionName()
    {
        $question = new Question(
            'Enter extension name (etc. my_extension): '
        );
        $this->validators->validateExtensionExist($question);
        return $this->run->getQuestionHelper()->ask(
            $this->run->getInput(),
            $this->run->getOutput(),
            $question
        );
    }

    /**
     * @param $name
     * @return mixed
     */
    public function askElementName($name)
    {
        $question = new Question(
            $name . ' name (etc. NewElement), without spaces: '
        );
        $this->validators->validateNotEmpty($question);
        return $this->run->getQuestionHelper()->ask(
            $this->run->getInput(),
            $this->run->getOutput(),
            $question
        );
    }

    /**
     * @param $name
     * @return mixed
     */
    public function askElementDescription($name)
    {
        $question = new Question(
            $name . ' description (etc. New Element description):  '
        );
        $this->validators->validateNotEmpty($question);
        return $this->run->getQuestionHelper()->ask(
            $this->run->getInput(),
            $this->run->getOutput(),
            $question
        );
    }

    /**
     * @return mixed
     */
    public function askPluginController()
    {
        $question = new Question(
            'Enter name of plugin Controller :  '
        );
        $this->validators->validateNotEmpty($question);
        return $this->run->getQuestionHelper()->ask(
            $this->run->getInput(),
            $this->run->getOutput(),
            $question
        );
    }

    /**
     * @return mixed
     */
    public function askPluginAction()
    {
        $question = new Question(
            'Enter name of plugin Action :  '
        );
        $this->validators->validateNotEmpty($question);
        return $this->run->getQuestionHelper()->ask(
            $this->run->getInput(),
            $this->run->getOutput(),
            $question
        );
    }

    /**
     * @param $name
     * @param $default
     * @return mixed
     */
    public function askTable($name, $default)
    {
        $question = new Question(
            'Enter table of ' . lcfirst($name) . ' (' . $default . ') : ',
            $default
        );
        $this->validators->validateNotEmpty($question);
        return $this->run->getQuestionHelper()->ask(
            $this->run->getInput(),
            $this->run->getOutput(),
            $question
        );
    }

    /**
     * @return mixed
     */
    public function askPageTypeDoktype()
    {
        $question = new Question(
            'Enter doktype of new page type : '
        );
        $this->validators->validateIsNumeric($question);
        return $this->run->getQuestionHelper()->ask(
            $this->run->getInput(),
            $this->run->getOutput(),
            $question
        );
    }

    /**
     * @return bool
     */
    public function needPageTypeAutoHeader()
    {
        $question = new ChoiceQuestion(
            'Do you want custom page type header?',
            [self::YES_SHORTCUT => self::YES, self::NO_SHORTCUT => self::NO]
        );
        return $this->run->getQuestionHelper()
            ->ask($this->run->getInput(), $this->run->getOutput(), $question) === self::YES_SHORTCUT ? true : false;
    }

    /**
     * @return mixed
     */
    public function askFieldName()
    {
        $question = new Question(self::getColoredDeepLevel() . 'Field name (etc. new_field): ');
        $this->validators->validateNotEmpty($question);
        return $this->run->getQuestionHelper()->ask(
            $this->run->getInput(),
            $this->run->getOutput(),
            $question
        );
    }

    /**
     * @return mixed
     */
    public function askFieldType()
    {
        $question = new ChoiceQuestion(
            self::getColoredDeepLevel() . 'Field type:',
            array_keys(
                $this->run->getFieldTypes()
            )
        );
        return $this->run->getQuestionHelper()->ask(
            $this->run->getInput(),
            $this->run->getOutput(),
            $question
        );
    }

    /**
     * @return mixed
     */
    public function askFieldTitle()
    {
        $question = new Question(self::getColoredDeepLevel() . 'Field title (etc. New-Field): ');
        $this->validators->validateNotEmpty($question);
        return $this->run->getQuestionHelper()->ask(
            $this->run->getInput(),
            $this->run->getOutput(),
            $question
        );
    }

    /**
     * @return mixed
     */
    public function askItemName()
    {
        $question = new Question(self::getColoredDeepLevel() . 'Item name (etc. item): ');
        $this->validators->validateNotEmpty($question);
        return $this->run->getQuestionHelper()->ask(
            $this->run->getInput(),
            $this->run->getOutput(),
            $question
        );
    }

    /**
     * @return mixed
     */
    public function askItemValue()
    {
        $question = new Question(self::getColoredDeepLevel() . 'Item value (etc. 0 or some string): ');
        $this->validators->validateNotEmpty($question);
        return $this->run->getQuestionHelper()->ask(
            $this->run->getInput(),
            $this->run->getOutput(),
            $question
        );
    }

    /**
     * @return mixed
     */
    public function askItemTitle()
    {
        $question = new Question(self::getColoredDeepLevel() . 'Item title (etc. New-Item): ');
        $this->validators->validateNotEmpty($question);
        return $this->run->getQuestionHelper()->ask(
            $this->run->getInput(),
            $this->run->getOutput(),
            $question
        );
    }

    /**
     * @return mixed
     */
    public function askInlineClassName()
    {
        $question = new Question(self::getColoredDeepLevel() . 'Inline Class name (etc. Inline): ');
        $this->validators->validateNotEmpty($question);
        return $this->run->getQuestionHelper()->ask(
            $this->run->getInput(),
            $this->run->getOutput(),
            $question
        );
    }

    /**
     * @return mixed
     */
    public function askInlineTitle()
    {
        $question = new Question(self::getColoredDeepLevel() . 'Inline title (etc. Inline): ');
        $this->validators->validateNotEmpty($question);
        return $this->run->getQuestionHelper()->ask(
            $this->run->getInput(),
            $this->run->getOutput(),
            $question
        );
    }

    /**
     * @return bool
     */
    public function needCreateMoreFields()
    {
        $question = new ChoiceQuestion(
            self::getColoredDeepLevel() . 'Do you want to create more fields?',
            [self::YES_SHORTCUT => self::YES, self::NO_SHORTCUT => self::NO]
        );

        return $this->run->getQuestionHelper()
                ->ask($this->run->getInput(), $this->run->getOutput(), $question) === self::YES_SHORTCUT;
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

    /**
     * @return bool
     */
    public function needCreateMoreItems()
    {
        $question = new ChoiceQuestion(
            self::getColoredDeepLevel() . 'Do you want to create more items?',
            [self::YES_SHORTCUT => self::YES, self::NO_SHORTCUT => self::NO]
        );

        return $this->run->getQuestionHelper()
                ->ask($this->run->getInput(), $this->run->getOutput(), $question) === self::YES_SHORTCUT;
    }
}
