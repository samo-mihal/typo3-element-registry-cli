<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\FlexFormFieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\Typo3FieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\ContentElementCreateCommand;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\PageTypeCreateCommand;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\PluginCreateCommand;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RecordCreateCommand;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\CheckRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Run\QuestionsRun;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Run\ValidatorsRun;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\Fields\FlexForm\FlexFormFieldsSetup;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\FieldsSetup;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class RunCreateElementCommand
 * @package Digitalwerk\Typo3ElementRegistryCli\Command
 */
class RunCreateElementCommand extends Command
{
    const CONTENT_ELEMENT = 'Content element';
    const PAGE_TYPE = 'Page Type';
    const PLUGIN = 'Plugin';
    const RECORD = 'Record';

    /**
     * @var ValidatorsRun
     */
    protected $validators = null;

    /**
     * @var QuestionsRun
     */
    protected $questions = null;

    /**
     * @var InputInterface
     */
    protected $input = null;

    /**
     * @var OutputInterface
     */
    protected $output = null;

    /**
     * @var string
     */
    protected $vendor = '';

    /**
     * @var mixed
     */
    protected $questionHelper = null;

    /**
     * @var array
     */
    protected $fieldTypes = [];

    /**
     * @var string
     */
    protected $table = '';

    /**
     * @return ValidatorsRun
     */
    public function getValidators(): ValidatorsRun
    {
        return $this->validators;
    }

    /**
     * @param ValidatorsRun $validators
     */
    public function setValidators(ValidatorsRun $validators): void
    {
        $this->validators = $validators;
    }

    /**
     * @return string|null
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getVendor(): ? string
    {
        return $this->getTypo3ElementRegistryCliExtensionConfiguration()['elementsVendor'];
    }

    /**
     * @return string|null
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getMainExtension(): ? string
    {
        return explode(
            '/',
            explode(':',$this->getContentElementRegistryExtensionConfiguration()['contentElementsPaths'])[1]
        )[0];;
    }

    /**
     * @return string|null
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getMainExtensionInNameSpaceFormat(): ? string
    {
        return str_replace(' ','',ucwords(str_replace('_',' ', $this->getMainExtension())));
    }

    /**
     * @return mixed
     */
    public function getQuestionHelper()
    {
        return $this->questionHelper;
    }

    /**
     * @return array
     */
    public function getFieldTypes(): array
    {
        return $this->fieldTypes;
    }

    /**
     * @param array $fieldTypes
     */
    public function setFieldTypes(array $fieldTypes): void
    {
        $this->fieldTypes = $fieldTypes;
    }

    /**
     * @param mixed $questionHelper
     */
    public function setQuestionHelper($questionHelper): void
    {
        $this->questionHelper = $questionHelper;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @param string $table
     */
    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    /**
     * @return InputInterface
     */
    public function getInput(): InputInterface
    {
        return $this->input;
    }

    /**
     * @param InputInterface $input
     */
    public function setInput(InputInterface $input): void
    {
        $this->input = $input;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    /**
     * @return mixed
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getContentElementRegistryExtensionConfiguration()
    {
        return GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('content_element_registry');
    }

    /**
     * @return mixed
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getTypo3ElementRegistryCliExtensionConfiguration()
    {
        return GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('typo3_element_registry_cli');
    }

    /**
     * @return QuestionsRun
     */
    public function getQuestions(): QuestionsRun
    {
        return $this->questions;
    }

    /**
     * @param QuestionsRun $questions
     */
    public function setQuestions(QuestionsRun $questions): void
    {
        $this->questions = $questions;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setOutput($output);
        $this->setInput($input);
        $this->setQuestionHelper($this->getHelper('question'));
        $this->setValidators(GeneralUtility::makeInstance(ValidatorsRun::class, $this));
        $this->validators->validateContentElementRegistrySettings();
        $this->validators->validateTypo3ElementRegistryCliSettings();
        $this->validators->validateCreateCommandConfigDataStructure();
        $this->setQuestions(GeneralUtility::makeInstance(QuestionsRun::class, $this));
        $questions = $this->getQuestions();

        $output->writeln('Welcome in Typo3 element registry');

        $question = new ChoiceQuestion(
            'What do you want to create?',
            [self::CONTENT_ELEMENT,self::PAGE_TYPE, self::PLUGIN, self::RECORD]
        );
        $needCreate = $this->getQuestionHelper()->ask($input, $output, $question);

        if ($needCreate === self::CONTENT_ELEMENT) {
            $this->addArgument('extension');
            $this->addArgument('vendor');
            $this->addArgument('table');
            $this->addArgument('name');
            $this->addArgument('title');
            $this->addArgument('description');
            $this->addArgument('fields');
            $this->addArgument('inline-fields');

            $input->setArgument(
                'extension',
                $this->getMainExtension()
            );
            $input->setArgument(
                'vendor',
                $this->getVendor()
            );
            $input->setArgument(
                'name',
                $questions->askElementName(self::CONTENT_ELEMENT)
            );
            $input->setArgument(
                'title',
                $questions->askElementTitle(self::CONTENT_ELEMENT)
            );
            $input->setArgument(
                'description',
                $questions->askElementDescription(self::CONTENT_ELEMENT)
            );
            $this->setTable(
                ContentElementCreateCommand::TABLE
            );
            $input->setArgument(
                'table',
                $this->getTable()
            );
            $input = $questions->askTCAFields($input);

            GeneralUtility::makeInstance(ContentElementCreateCommand::class)->execute($input, $output);
        } elseif ($needCreate === self::PAGE_TYPE) {
            $this->addArgument('main-extension');
            $this->addArgument('extension');
            $this->addArgument('vendor');
            $this->addArgument('table');
            $this->addArgument('name');
            $this->addArgument('title');
            $this->addArgument('doktype');
            $this->addArgument('auto-header');
            $this->addArgument('fields');
            $this->addArgument('inline-fields');

            $input->setArgument(
                'extension',
                $questions->askExtensionName()
            );
            $input->setArgument(
                'main-extension',
                $this->getMainExtension()
            );
            $input->setArgument(
                'vendor',
                $this->getVendor()
            );
            $this->setTable(
                PageTypeCreateCommand::TABLE
            );
            $input->setArgument(
                'doktype',
                $questions->askPageTypeDoktype()
            );
            $input->setArgument(
                'name',
                $questions->askElementName(self::PAGE_TYPE)
            );
            $input->setArgument(
                'title',
                $questions->askElementTitle(self::PAGE_TYPE)
            );
            $input->setArgument(
                'auto-header',
                $questions->needPageTypeAutoHeader()
            );
            $input->setArgument(
                'table',
                $this->getTable()
            );
            $input = $questions->askTCAFields($input);

            GeneralUtility::makeInstance(PageTypeCreateCommand::class)->execute($input, $output);
        } elseif ($needCreate === self::PLUGIN) {
            $this->addArgument('name');
            $this->addArgument('title');
            $this->addArgument('description');
            $this->addArgument('controller');
            $this->addArgument('action');
            $this->addArgument('fields');
            $this->addArgument('main-extension');
            $this->addArgument('extension');
            $this->addArgument('vendor');

            $input->setArgument(
                'extension',
                $questions->askExtensionName()
            );
            $input->setArgument(
                'main-extension',
                $this->getMainExtension()
            );
            $input->setArgument(
                'vendor',
                $this->getVendor()
            );
            $input->setArgument(
                'name',
                $questions->askElementName(self::PLUGIN)
            );
            $input->setArgument(
                'title',
                $questions->askElementTitle(self::PLUGIN)
            );
            $input->setArgument(
                'description',
                $questions->askElementDescription(self::PLUGIN)
            );
            $input->setArgument(
                'controller',
                $questions->askPluginController()
            );
            $input->setArgument(
                'action',
                $questions->askPluginAction()
            );

            $input = $questions->askFlexFormFields($input);
            GeneralUtility::makeInstance(PluginCreateCommand::class)->execute($input, $output);
        } elseif ($needCreate === self::RECORD) {
            $this->addArgument('name');
            $this->addArgument('title');
            $this->addArgument('fields');
            $this->addArgument('main-extension');
            $this->addArgument('extension');
            $this->addArgument('vendor');
            $this->addArgument('inline-fields');

            $input->setArgument(
                'extension',
                $questions->askExtensionName()
            );
            $input->setArgument(
                'main-extension',
                $this->getMainExtension()
            );
            $input->setArgument(
                'vendor',
                $this->getVendor()
            );
            $input->setArgument(
                'name',
                $questions->askElementName(self::RECORD)
            );
            $input->setArgument(
                'title',
                $questions->askElementTitle(self::RECORD)
            );

            $input = $questions->askTCAFields($input);
            GeneralUtility::makeInstance(RecordCreateCommand::class)->execute($input, $output);
        }
    }
}
