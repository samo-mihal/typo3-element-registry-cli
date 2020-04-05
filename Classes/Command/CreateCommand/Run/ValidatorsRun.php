<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Run;

use Digitalwerk\Typo3ElementRegistryCli\Command\RunCreateElementCommand;
use InvalidArgumentException;
use Symfony\Component\Console\Question\Question;

/**
 * Class Validators
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Run
 */
class ValidatorsRun
{
    /**
     * @var RunCreateElementCommand
     */
    protected $run = null;

    /**
     * ValidatorsRun constructor.
     * @param RunCreateElementCommand $run
     */
    public function __construct(RunCreateElementCommand $run)
    {
        $this->run = $run;
    }

    /**
     * @param Question $question
     */
    public function validateNotEmpty(Question $question) {
        $question->setValidator(function ($answer) {
            if (empty(trim($answer))) {
                throw new \RuntimeException(
                    'Answer can not be empty.'
                );
            }
            return $answer;
        });
    }

    /**
     * @param Question $question
     */
    public function validateExtensionExist(Question $question) {
        $question->setValidator(function ($answer) {

            if (empty(trim($answer)) || !file_exists('public/typo3conf/ext/' . trim($answer))) {
                throw new \RuntimeException(
                    'Extension does not exist.'
                );
            }
            return $answer;
        });
    }

    /**
     * @param Question $question
     */
    public function validateIsNumeric(Question $question) {
        $question->setValidator(function ($answer) {
            if (!is_numeric($answer)) {
                throw new \RuntimeException(
                    'Answer must be numeric.'
                );
            }
            if (in_array($answer, array_keys($GLOBALS['TCA'][$this->run->getTable()]['types']))) {
                throw new \RuntimeException(
                    'Page type with doktype ' . $answer . ' already exist.'
                );
            }
            return $answer;
        });
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function validateTypo3ElementRegistryCliSettings()
    {
        if (empty($this->run->getVendor())) {
            throw new InvalidArgumentException('Fill in typo3_element_registry_cli extension settings.');
        }
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function validateContentElementRegistrySettings()
    {
        if (empty($this->run->getMainExtension())) {
            throw new InvalidArgumentException('Fill in content_element_registry extension settings.');
        }
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function validateCreateCommandConfigDataStructure()
    {
        $mainExtension = $this->run->getMainExtension();
        $mainExtensionInNameSpaceFormat = $this->run->getMainExtensionInNameSpaceFormat();
        $vendor = $this->run->getVendor();

        if (!file_exists('public/typo3conf/ext/' . $mainExtension . '/Classes/CreateCommandConfig/CreateCommandCustomData.php'))
        {
            if (!file_exists('public/typo3conf/ext/' . $mainExtension . '/Classes/CreateCommandConfig/')) {
                mkdir('public/typo3conf/ext/' . $mainExtension . '/Classes/CreateCommandConfig/', 0777, true);
            }

            file_put_contents(
                'public/typo3conf/ext/' . $mainExtension . '/Classes/CreateCommandConfig/CreateCommandCustomData.php',
                '<?php
namespace ' . $vendor . '\\' . $mainExtensionInNameSpaceFormat . '\CreateCommandConfig;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Fields\FieldObject;

/**
 * Class CreateCommandCustomData
 * @package ' . $vendor . '\\' . $mainExtensionInNameSpaceFormat . '\CreateCommandConfig
 */
class CreateCommandCustomData
{
       /**
     * @return array
     */
    public function typo3TcaFieldTypes()
    {
        return [];
    }

    /**
     * @param FieldObject $field
     * @return array
     */
    public function newTcaFieldsConfigs(FieldObject $field)
    {
        return [];
    }

    /**
     * @param FieldObject $field
     * @return array
     */
    public function newTcaFieldsModelDescription(FieldObject $field)
    {
        return [];
    }

    /**
     * @return array
     */
    public function typo3FlexFormFieldTypes()
    {
        return [];
    }

    /**
     * @return array
     */
    public function traitsAndClasses()
    {
        return [];
    }

    public function overrideClasses() {
        return [
            \'contentElementExtendClass\' => \'\',
            \'contentElementInlineModelExtendClass\' => \'\',
            \'pageTypeInlineModelExtendClass\' => \'\',
            \'recordModelExtendClass\' => \'\',
            \'pageTypeModelExtendClass\' => \'\',
            \'pluginControllerExtendClass\' => \'\',
            \'iconRegisterClass\' => \'\',
            \'registerPageDoktypeClass\' => \'\'
        ];
    }

    /**
     * In typoScript constants make marker "#Page types"
     * Fill in typoScript constants path (e.g. public/typo3conf/ext/extension_name/typoscript_constants.typoscript)
     * @return string
     */
    public function pathToTypoScriptConstants()
    {
        return \'\';
    }
}
'
            );
        }
    }
}
