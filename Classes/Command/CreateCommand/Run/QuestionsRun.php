<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Run;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\FlexFormFieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\Typo3FieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\ContentElementCreateCommand;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\PageTypeCreateCommand;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Run\Questions\AbstractQuestions;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\AdvanceFieldsSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields\FlexForm\FlexFormFieldsSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\FieldsSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\RunCreateElementCommand;
use Digitalwerk\Typo3ElementRegistryCli\Utility\FieldsCreateCommandUtility;
use InvalidArgumentException;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class QuestionsRun
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Run
 */
class QuestionsRun extends AbstractQuestions
{
    /**
     * Element type constants
     */
    const CONTENT_ELEMENT = 'Content element';
    const PAGE_TYPE = 'Page Type';
    const PLUGIN = 'Plugin';
    const RECORD = 'Record';

    const YES_SHORTCUT = 'y';
    const NO_SHORTCUT = 'n';
    const YES = 'Yes';
    const NO = 'No';
    const DEEP_LEVEL_SPACES = ">>>";

    /**
     * QuestionsRun constructor.
     * @param RunCreateElementCommand $run
     */
    public function __construct(RunCreateElementCommand $run)
    {
        parent::__construct($run);
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
     * @return \Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject|null
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function initialize()
    {
        $this->askElementType();

        if ($this->elementObject->getType() === self::CONTENT_ELEMENT){
            $this->elementObject->setTable(ContentElementCreateCommand::TABLE);
            $this->askExtensionName();
            $this->askElementName();
            $this->askElementDescription();
            $this->askElementTitle();
            $this->askTCAFields();
            GeneralUtility::makeInstance(ContentElementCreateCommand::class)->execute($this->elementObject);
        } elseif ($this->elementObject->getType() === self::PAGE_TYPE) {
            $this->elementObject->setTable(PageTypeCreateCommand::TABLE);
            $this->askExtensionName();
            $this->askElementName();
            $this->askElementTitle();
            $this->askPageTypeDoktype();
            $this->needPageTypeAutoHeader();
            $this->askTCAFields();
//            GeneralUtility::makeInstance(PageTypeCreateCommand::class)->execute($this->elementObject);
        } elseif ($this->elementObject->getType() === self::PLUGIN) {
            $this->askExtensionName();
            $this->askElementName();
            $this->askElementTitle();
            $this->askElementDescription();
            $this->askPluginController();
            $this->askPluginAction();
            $this->askFlexFormFields();
        } elseif ($this->elementObject->getType() === self::RECORD) {
            $this->askElementName();
            $this->askElementTitle();
            $this->askExtensionName();
            $this->askTCAFields();
        }

        return $this->elementObject;
    }

    /**
     * @return mixed
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function askFlexFormFields()
    {
        if ($this->needCreateFields()) {
            $this->run->setFieldTypes(
                GeneralUtility::makeInstance(FlexFormFieldTypesConfig::class)->getFlexFormFieldTypes()
            );
            $flexFormFields = new FlexFormFieldsSetup($this->run);
            $flexFormFields->createField();

            $this->elementObject->setFields(
                $flexFormFields->getFields()
            );
        } else {
            $this->elementObject->setFields(null);
        }
    }

    /**
     * @return void
     */
    public function askPluginController(): void
    {
        $question = new Question(
            'Enter name of plugin Controller :  '
        );
        $this->validators->validateNotEmpty($question);
        $this->elementObject->setControllerName(
            $this->run->getQuestionHelper()->ask(
                $this->run->getInput(),
                $this->run->getOutput(),
                $question
            )
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
        $this->elementObject->setActionName(
            $this->run->getQuestionHelper()->ask(
                $this->run->getInput(),
                $this->run->getOutput(),
                $question
            )
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
        $this->elementObject->setDoktype(
            $this->run->getQuestionHelper()->ask(
                $this->run->getInput(),
                $this->run->getOutput(),
                $question
            )
        );

    }

    /**
     * @return void
     */
    public function needPageTypeAutoHeader(): void
    {
        $question = new ChoiceQuestion(
            'Do you want custom page type header?',
            [self::YES_SHORTCUT => self::YES, self::NO_SHORTCUT => self::NO]
        );
        $this->elementObject->setAutoHeader(
            $this->run->getQuestionHelper()
                ->ask($this->run->getInput(), $this->run->getOutput(), $question) === self::YES_SHORTCUT
        );
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

    /**
     * @return void
     */
    public function askElementType(): void
    {
        $question = new ChoiceQuestion(
            'What do you want to create?',
            [
                self::CONTENT_ELEMENT,
                self::PAGE_TYPE,
                self::PLUGIN,
                self::RECORD
            ]
        );
        $this->elementObject->setType(
            $this->run->getQuestionHelper()->ask(
                $this->input,
                $this->output,
                $question
            )
        );
    }

    /**
     * @return void
     */
    public function askExtensionName(): void
    {
        if ($this->elementObject->getType() === self::CONTENT_ELEMENT)
        {
            $this->elementObject->setExtensionName(
                $this->elementObject->getMainExtension()
            );
        } else {
            $question = new Question(
                'Enter extension name (etc. my_extension): '
            );
            $this->validators->validateExtensionExist($question);
            $this->elementObject->setExtensionName(
                $this->run->getQuestionHelper()->ask(
                    $this->run->getInput(),
                    $this->run->getOutput(),
                    $question
                )
            );
        }
    }

    /**
     * @return void
     */
    public function askElementName(): void
    {
        $question = new Question(
            $this->elementObject->getType() . ' name (etc. NewElement), without spaces: '
        );
        $this->validators->validateNotEmpty($question);
        $this->elementObject->setName(
            $this->run->getQuestionHelper()->ask(
                $this->run->getInput(),
                $this->run->getOutput(),
                $question
            )
        );
    }

    /**
     * @return void
     */
    public function askElementDescription(): void
    {
        $question = new Question(
            $this->elementObject->getType() . ' description (etc. New Element description):  '
        );
        $this->validators->validateNotEmpty($question);
        $this->elementObject->setDescription(
            $this->run->getQuestionHelper()->ask(
                $this->run->getInput(),
                $this->run->getOutput(),
                $question
            )
        );
    }

    /**
     * @return void
     */
    public function askElementTitle(): void
    {
        $question = new Question(
            $this->elementObject->getType() . ' title (etc. New Element): '
        );
        $this->validators->validateNotEmpty($question);
        $this->elementObject->setTitle(
            $this->run->getQuestionHelper()->ask(
                $this->run->getInput(),
                $this->run->getOutput(),
                $question
            )
        );
    }

    /**
     * @return void
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function askTCAFields()
    {
        $table = $this->elementObject->getTable();
        if ($this->needCreateFields()) {
            $this->run->setFieldTypes(
                GeneralUtility::makeInstance(Typo3FieldTypesConfig::class)
                    ->getTCAFieldTypes($table)[$table]
            );

            $fieldsSetup = new FieldsSetup($this->run);
            $fieldsSetup->createField($table);
            $this->elementObject->setFields(
                $fieldsSetup->getFields()
            );
            $this->elementObject->setAreAllFieldsDefault(
                FieldsCreateCommandUtility::areAllFieldsDefault(
                    $this->elementObject->getFields(),
                    $table
                )
            );
            $this->elementObject->setInlineFields(
                AdvanceFieldsSetup::getAdvanceFields()
            );
        } else {
            $this->elementObject->setFields(null);
        }
    }
}
