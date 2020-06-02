<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\FlexFormFieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\Typo3FieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element\FieldsSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Element\Fields\FlexForm\FlexFormFieldsSetup;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;
use http\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class QuestionsSetup
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Run
 */
class QuestionsSetup
{
    /**
     * Constants of questionSetup
     */
    const YES_SHORTCUT = 'y';
    const NO_SHORTCUT = 'n';
    const YES = 'Yes';
    const NO = 'No';
    const DEEP_LEVEL_SPACES = ">>>";

    /**
     * @var ElementSetup
     */
    protected $elementSetup = null;

    /**
     * @var InputInterface
     */
    protected $input = null;

    /**
     * @var OutputInterface
     */
    protected $output = null;

    /**
     * @var ElementObject
     */
    protected $elementObject = null;

    /**
     * @var ValidatorsSetup
     */
    protected $validators = null;

    /**
     * QuestionsSetup constructor.
     * @param ElementSetup $elementSetup
     * @param ValidatorsSetup $validators
     */
    public function __construct(ElementSetup $elementSetup, ValidatorsSetup $validators)
    {
        $this->elementSetup = $elementSetup;
        $this->input = $this->elementSetup->getElementObject()->getInput();
        $this->output = $this->elementSetup->getElementObject()->getOutput();
        $this->elementObject = $this->elementSetup->getElementObject();
        $this->validators = $validators;

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
        self::$deepLevel = QuestionsSetup::getRawDeepLevel() . QuestionsSetup::DEEP_LEVEL_SPACES;
    }

    public static function setDeepLevelUp(): void
    {
        self::$deepLevel = substr(QuestionsSetup::getRawDeepLevel(), 0, -strlen(QuestionsSetup::DEEP_LEVEL_SPACES));
    }

    /**
     * @return string
     */
    public function askPluginController(): string
    {
        $question = new Question(
            'Enter name of plugin Controller :  '
        );
        $this->validators->validateNotEmpty($question);

        return $this->elementSetup->getQuestionHelper()->ask(
                $this->elementSetup->getInput(),
                $this->elementSetup->getOutput(),
                $question
            );
    }

    /**
     * @return string
     */
    public function askPluginAction(): string
    {
        $question = new Question(
            'Enter name of plugin Action :  '
        );
        $this->validators->validateNotEmpty($question);

        return $this->elementSetup->getQuestionHelper()->ask(
                $this->elementSetup->getInput(),
                $this->elementSetup->getOutput(),
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
        return $this->elementSetup->getQuestionHelper()->ask(
                $this->elementSetup->getInput(),
                $this->elementSetup->getOutput(),
                $question
            );

    }

    /**
     * @return bool
     */
    public function needPageTypeAutoHeader(): bool
    {
        $question = new ChoiceQuestion(
            'Do you want custom page type header?',
            [self::YES_SHORTCUT => self::YES, self::NO_SHORTCUT => self::NO]
        );

        return $this->elementSetup->getQuestionHelper()
                ->ask($this->elementSetup->getInput(), $this->elementSetup->getOutput(), $question) === self::YES_SHORTCUT;
    }

    /**
     * @return mixed
     */
    public function askFieldName()
    {
        $question = new Question(self::getColoredDeepLevel() . 'Field name (etc. new_field): ');
        $this->validators->validateNotEmpty($question);
        return $this->elementSetup->getQuestionHelper()->ask(
            $this->elementSetup->getInput(),
            $this->elementSetup->getOutput(),
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
                $this->elementSetup->getFieldTypes()
            )
        );
        return $this->elementSetup->getQuestionHelper()->ask(
            $this->elementSetup->getInput(),
            $this->elementSetup->getOutput(),
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
        return $this->elementSetup->getQuestionHelper()->ask(
            $this->elementSetup->getInput(),
            $this->elementSetup->getOutput(),
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
        return $this->elementSetup->getQuestionHelper()->ask(
            $this->elementSetup->getInput(),
            $this->elementSetup->getOutput(),
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
        return $this->elementSetup->getQuestionHelper()->ask(
            $this->elementSetup->getInput(),
            $this->elementSetup->getOutput(),
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
        return $this->elementSetup->getQuestionHelper()->ask(
            $this->elementSetup->getInput(),
            $this->elementSetup->getOutput(),
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
        return $this->elementSetup->getQuestionHelper()->ask(
            $this->elementSetup->getInput(),
            $this->elementSetup->getOutput(),
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
        return $this->elementSetup->getQuestionHelper()->ask(
            $this->elementSetup->getInput(),
            $this->elementSetup->getOutput(),
            $question
        );
    }

    /**
     * @return mixed
     */
    public function askInlineForeignField()
    {
        $question = new Question(self::getColoredDeepLevel() . 'Inline foreign field (etc. foreign_field): ');
        $this->validators->validateNotEmpty($question);
        return $this->elementSetup->getQuestionHelper()->ask(
            $this->elementSetup->getInput(),
            $this->elementSetup->getOutput(),
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

        return $this->elementSetup->getQuestionHelper()
                ->ask($this->elementSetup->getInput(), $this->elementSetup->getOutput(), $question) === self::YES_SHORTCUT;
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

        return $this->elementSetup->getQuestionHelper()
                ->ask($this->elementSetup->getInput(), $this->elementSetup->getOutput(), $question) === self::YES_SHORTCUT;
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

        return $this->elementSetup->getQuestionHelper()
                ->ask($this->elementSetup->getInput(), $this->elementSetup->getOutput(), $question) === self::YES_SHORTCUT;
    }

    /**
     * @return string
     */
    public function askElementType(): string
    {
        $question = new ChoiceQuestion(
            'Choose element type: ',
            [
                ElementSetup::CONTENT_ELEMENT,
                ElementSetup::PAGE_TYPE,
                ElementSetup::PLUGIN,
                ElementSetup::RECORD
            ]
        );

        return $this->elementSetup->getQuestionHelper()->ask(
            $this->input,
            $this->output,
            $question
        );
    }

    /**
     * @return string
     */
    public function askElement(): string
    {
        $extensionName = $this->elementObject->getExtensionName();
        $elementType = $this->elementObject->getType();
        $elements = GeneralCreateCommandUtility::getExistingElementsInExtension(
            $extensionName,
            $elementType
        );

        if ($elements) {
            $question = new ChoiceQuestion(
                'Choose element: ',
                $elements
            );
        } else {
            throw new \InvalidArgumentException(
                $elementType . ' elements do not exist in ' . $extensionName . ' extension'
            );
        }
        return trim(
            str_replace(
                '.php',
                '',
                $this->elementSetup->getQuestionHelper()
                    ->ask($this->input, $this->output, $question)
            )
        );
    }

    /**
     * @return string
     */
    public function askElementAction(): string
    {
        $question = new ChoiceQuestion(
            'What do you want to do?',
            [
                ElementSetup::CREATE_NEW_ELEMENT,
                ElementSetup::EDIT_EXISTING_ELEMENT
            ]
        );

        return $this->elementSetup->getQuestionHelper()->ask(
            $this->input,
            $this->output,
            $question
        );
    }

    /**
     * @return string
     */
    public function askEditElementAction(): string
    {
        $question = new ChoiceQuestion(
            'What do you want to do?',
            [
                ElementSetup::ADD_FIELDS_TO_EXISTING_ELEMENT,
            ]
        );

        return $this->elementSetup->getQuestionHelper()->ask(
            $this->input,
            $this->output,
            $question
        );
    }

    /**
     * @return string
     */
    public function askExtensionName(): string
    {
        if ($this->elementObject->getType() === ElementSetup::CONTENT_ELEMENT)
        {
            return $this->elementObject->getMainExtension();
        } else {
            $question = new ChoiceQuestion(
                'Select extension: ',
                GeneralCreateCommandUtility::getExtensions()
            );

            return $this->elementSetup->getQuestionHelper()->ask(
                $this->input,
                $this->output,
                $question
            );
        }
    }

    /**
     * @return string
     */
    public function askElementName(): string
    {
        $question = new Question(
            $this->elementObject->getType() . ' name (etc. NewElement), without spaces: '
        );
        $this->validators->validateNotEmpty($question);
        return $this->elementSetup->getQuestionHelper()->ask(
                $this->elementSetup->getInput(),
                $this->elementSetup->getOutput(),
                $question
            );
    }

    /**
     * @return string
     */
    public function askElementDescription(): string
    {
        $question = new Question(
            $this->elementObject->getType() . ' description (etc. New Element description):  '
        );
        $this->validators->validateNotEmpty($question);
        return $this->elementSetup->getQuestionHelper()->ask(
                $this->elementSetup->getInput(),
                $this->elementSetup->getOutput(),
                $question
            );
    }

    /**
     * @return string
     */
    public function askElementTitle(): string
    {
        $question = new Question(
            $this->elementObject->getType() . ' title (etc. New Element): '
        );
        $this->validators->validateNotEmpty($question);
        return $this->elementSetup->getQuestionHelper()->ask(
                $this->elementSetup->getInput(),
                $this->elementSetup->getOutput(),
                $question
            );
    }

    /**
     * @return mixed
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function askFlexFormFields()
    {
        if ($this->needCreateFields()) {
            $this->elementSetup->setFieldTypes(
                GeneralUtility::makeInstance(FlexFormFieldTypesConfig::class)->getFlexFormFieldTypes()
            );
            $flexFormFields = new FlexFormFieldsSetup($this->elementSetup);
            $flexFormFields->createField();

            $this->elementObject->setFields(
                $flexFormFields->getFields()
            );
        } else {
            $this->elementObject->setFields(null);
        }
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage|FieldObject[]
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function askTCAFields()
    {
        $table = $this->elementObject->getTable();
        if ($this->needCreateFields()) {
            $this->elementSetup->setFieldTypes(
                GeneralUtility::makeInstance(Typo3FieldTypesConfig::class)
                    ->getTCAFieldTypes($table)[$table]
            );

            $fieldsSetup = new FieldsSetup($this->elementSetup);
            $fieldsSetup->createField($table);

            return $fieldsSetup->getFields();
        } else {
            return null;
        }
    }
}
