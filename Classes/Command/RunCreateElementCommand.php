<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\FlexFormFieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config\Typo3FieldTypesConfig;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\ContentElementCreateCommand;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;
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
    /**
     * @var ValidatorsRun
     */
    protected $validators = null;

    /**
     * @var QuestionsRun
     */
    protected $questions = null;

    /**
     * @var ElementObject
     */
    protected $elementObject = null;

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
     * @return ElementObject
     */
    public function getElementObject(): ElementObject
    {
        return $this->elementObject;
    }

    /**
     * @param ElementObject $elementObject
     */
    public function setElementObject(ElementObject $elementObject): void
    {
        $this->elementObject = $elementObject;
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
        $elementObject = GeneralUtility::makeInstance(ElementObject::class);
        $elementObject->setMainExtension($this->getMainExtension());
        $elementObject->setVendor($this->getVendor());
        $elementObject->setOutput($output);
        $elementObject->setInput($input);
        $this->setOutput($output);
        $this->setInput($input);
        $this->setElementObject($elementObject);
        $this->setQuestionHelper($this->getHelper('question'));
        $this->setValidators(GeneralUtility::makeInstance(ValidatorsRun::class, $this));
        $this->validators->validateContentElementRegistrySettings();
        $this->validators->validateTypo3ElementRegistryCliSettings();
        $this->validators->validateCreateCommandConfigDataStructure();
        $questionRun = GeneralUtility::makeInstance(QuestionsRun::class, $this);
        $this->setQuestions($questionRun);

        $output->writeln('Welcome in Typo3 element registry');
        $questionRun->initialize();
    }
}
