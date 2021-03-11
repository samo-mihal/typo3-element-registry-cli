<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command;

use Digitalwerk\Typo3ElementRegistryCli\ElementObjects\RecordObject;
use Digitalwerk\Typo3ElementRegistryCli\Utility\FileUtility;
use Digitalwerk\Typo3ElementRegistryCli\Utility\TranslationUtility;
use Symfony\Component\Console\Question\ChoiceQuestion;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use function Symfony\Component\String\u;

/**
 * Class RecordMakeCommand
 * @package Digitalwerk\Typo3ElementRegistryCli\Command
 */
class RecordMakeCommand extends AbstractMakeCommand
{
    /**
     * Default constants
     */
    const DEFAULT_MODEL_PATH =
        'EXT:typo3_element_registry_cli/Resources/Private/Templates/Record/Model.txt';
    const DEFAULT_MODEL_EXTEND =
        'TYPO3\CMS\Extbase\DomainObject\AbstractEntity';

    /**
     * @var string
     */
    protected $modelTemplatePath = self::DEFAULT_MODEL_PATH;

    /**
     * @var string
     */
    protected $modelNamespace = '';

    /**
     * @var string
     */
    protected $modelPath = '';

    /**
     * @var array
     */
    protected $requiredFiles = [
        'EXT:{extension}/Resources/Private/Language/locallang_db.xlf'
    ];

    /**
     * @var RecordObject
     */
    protected $recordObject = null;

    /**
     * @return void
     */
    public function beforeMake(): void
    {
        $this->extension = $this->questionHelper->ask(
            $this->input,
            $this->output,
            (new ChoiceQuestion(
                'Plugin extension: ',
                array_keys($GLOBALS['TYPO3_LOADED_EXT'])
            )
            )
        );

        if ($this->typo3ElementRegistryCliConfig['record']['modelTemplatePath']) {
            $this->modelTemplatePath = $this->typo3ElementRegistryCliConfig['record']['modelTemplatePath'];
        }
        $this->modelTemplatePath = GeneralUtility::getFileAbsFileName($this->modelTemplatePath);

        $this->recordObject = (new RecordObject($this->input, $this->output, $this->questionHelper, $this));
        $this->recordObject->questions();

        $this->table = 'tx_' . strtolower(u($this->extension)->camel()) . '_domain_model_' .
            strtolower($this->recordObject->getName());
        parent::beforeMake();

        /** Init model path */
        $this->modelPath = GeneralUtility::getFileAbsFileName(
            'EXT:' . $this->extension . '/Classes/Domain/Model/' .
            $this->recordObject->getName() . '.php'
        );
        $this->modelNamespace = $this->vendor . '\\' . u($this->extension)->camel()->title(true) . '\\'
            . 'Domain\\Model';
    }

    /**
     * @return void
     */
    public function make(): void
    {
        /** Model */
        $modelTemplate = file_get_contents($this->modelTemplatePath);
        $modelTemplate = str_replace([
            '{recordName}', '{extend}', '{namespace}'
        ], [
            $this->recordObject->getName(),
            self::DEFAULT_MODEL_EXTEND,
            $this->modelNamespace
        ], $modelTemplate);
        FileUtility::createFile($this->modelPath, $modelTemplate);

        /** Write title and description to locallang */
        TranslationUtility::addStringToTranslation(
            'EXT:' . $this->extension . '/Resources/Private/Language/locallang_db.xlf',
            $this->table,
            $this->recordObject->getTitle()
        );
    }
}
