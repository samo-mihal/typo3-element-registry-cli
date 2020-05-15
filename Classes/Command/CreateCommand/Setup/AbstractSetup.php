<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;
use Digitalwerk\Typo3ElementRegistryCli\Utility\FieldsCreateCommandUtility;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class AbstractSetup
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup
 */
class AbstractSetup
{
    /**
     * @var array
     */
    protected $fieldTypes = [];

    /**
     * @var InputInterface
     */
    protected $input = null;

    /**
     * @var OutputInterface
     */
    protected $output = null;

    /**
     * @var FieldsCreateCommandUtility
     */
    protected $fieldsCreateCommandUtility = null;

    /**
     * @var ElementObject
     */
    protected $elementObject = null;

    /**
     * @var QuestionsSetup
     */
    protected $questions = null;

    /**
     * @var ValidatorsSetup
     */
    protected $validators = null;

    /**
     * @var mixed
     */
    protected $questionHelper = null;

    /**
     * AbstractSetup constructor.
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->fieldsCreateCommandUtility = GeneralUtility::makeInstance(FieldsCreateCommandUtility::class);
        $this->elementObject = $this->elementObject ?: GeneralUtility::makeInstance(ElementObject::class);
        $this->elementObject->setVendor($this->getVendor());
        $this->elementObject->setOutput($output);
        $this->elementObject->setInput($input);
        $this->input = $input;
        $this->output = $output;
        $this->validators = GeneralUtility::makeInstance(ValidatorsSetup::class, $this);
        $this->questions = GeneralUtility::makeInstance(QuestionsSetup::class, $this, $this->validators);
    }

    /**
     * @return object|\Psr\Log\LoggerAwareInterface|ExtensionConfiguration|\TYPO3\CMS\Core\SingletonInterface
     */
    public static function getExtensionConfiguration()
    {
        return GeneralUtility::makeInstance(ExtensionConfiguration::class);
    }

    /**
     * @return ElementObject
     */
    public function getElementObject(): ElementObject
    {
        return $this->elementObject;
    }

    /**
     * @return mixed
     */
    public function getQuestionHelper()
    {
        return $this->questionHelper;
    }

    /**
     * @param mixed $questionHelper
     */
    public function setQuestionHelper($questionHelper): void
    {
        $this->questionHelper = $questionHelper;
    }

    /**
     * @return InputInterface
     */
    public function getInput(): InputInterface
    {
        return $this->input;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    /**
     * @return string|null
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public static function getVendor(): ? string
    {
        return self::getTypo3ElementRegistryCliExtensionConfiguration()['elementsVendor'];
    }

    /**
     * @return string|null
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public static function getMainExtension(): ? string
    {
        return explode(
            '/',
            explode(':', self::getContentElementRegistryExtensionConfiguration()['contentElementsPaths'])[1]
        )[0];;
    }

    /**
     * @return mixed
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public static function getContentElementRegistryExtensionConfiguration()
    {
        return self::getExtensionConfiguration()->get('content_element_registry');
    }

    /**
     * @return mixed
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public static function getTypo3ElementRegistryCliExtensionConfiguration()
    {
        return self::getExtensionConfiguration()->get('typo3_element_registry_cli');
    }

    /**
     * @return string|null
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public static function getMainExtensionInNameSpaceFormat(): ? string
    {
        return str_replace(' ','',ucwords(str_replace('_',' ', self::getMainExtension())));
    }

    /**
     * @return QuestionsSetup
     */
    public function getQuestions(): QuestionsSetup
    {
        return $this->questions;
    }

    /**
     * @return array
     */
    public function getFieldTypes(): array
    {
        return $this->fieldTypes;
    }

    /**
     * @param array|null $fieldTypes
     */
    public function setFieldTypes(? array $fieldTypes): void
    {
        $this->fieldTypes = $fieldTypes;
    }
}
