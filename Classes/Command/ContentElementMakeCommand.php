<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command;

use Digitalwerk\Typo3ElementRegistryCli\ElementObjects\ContentElementObject;
use Digitalwerk\Typo3ElementRegistryCli\Utility\FileUtility;
use Digitalwerk\Typo3ElementRegistryCli\Utility\ImageUtility;
use Digitalwerk\Typo3ElementRegistryCli\Utility\TranslationUtility;
use Symfony\Component\Console\Question\ChoiceQuestion;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use function Symfony\Component\String\u;

/**
 * Class ContentElementMakeCommand
 * @package Digitalwerk\Typo3ElementRegistryCli\Command
 */
class ContentElementMakeCommand extends AbstractMakeCommand
{
    /**
     * Default constants
     */
    const DEFAULT_TEMPLATE_PATH =
        'EXT:typo3_element_registry_cli/Resources/Private/Templates/ContentElement/Template.txt';
    const DEFAULT_CLASS_PATH =
        'EXT:typo3_element_registry_cli/Resources/Private/Templates/ContentElement/Class.txt';
    const DEFAULT_MODEL_PATH =
        'EXT:typo3_element_registry_cli/Resources/Private/Templates/ContentElement/Model.txt';
    const DEFAULT_CLASS_EXTEND =
        'Digitalwerk\ContentElementRegistry\ContentElement\AbstractContentElementRegistryItem';
    const DEFAULT_MODEL_EXTEND =
        'Digitalwerk\ContentElementRegistry\Domain\Model\ContentElement';

    /**
     * @var string
     */
    protected $templateTemplatePath = self::DEFAULT_TEMPLATE_PATH;

    /**
     * @var string
     */
    protected $classTemplatePath = self::DEFAULT_CLASS_PATH;

    /**
     * @var string
     */
    protected $modelTemplatePath = self::DEFAULT_MODEL_PATH;

    /**
     * @var string
     */
    protected $classExtend = self::DEFAULT_CLASS_EXTEND;

    /**
     * @var string
     */
    protected $modelExtend = self::DEFAULT_MODEL_EXTEND;

    /**
     * @var string
     */
    protected $classNamespace = '';

    /**
     * @var string
     */
    protected $modelNamespace = '';

    /**
     * @var array
     */
    protected $requiredFiles = [
        'EXT:{extension}/Resources/Private/Language/locallang_db.xlf'
    ];

    /**
     * @var string
     */
    public $table = 'tt_content';

    /**
     * @var ContentElementObject
     */
    protected $contentElementObject = null;

    /**
     * @var string
     */
    protected $classPath = '';

    /**
     * @var string
     */
    protected $modelPath = '';

    /**
     * @var string
     */
    protected $templatePath = '';

    /**
     * @return void
     */
    public function beforeMake(): void
    {
        if (ExtensionManagementUtility::isLoaded('content_element_registry') === false) {
            throw new \InvalidArgumentException('Extension content_element_registry is not loaded.');
        }
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('content_element_registry');
        $contentElementsPaths = explode(',', $extensionConfiguration['contentElementsPaths']);
        if (!empty($contentElementsPaths)) {
            if (count($contentElementsPaths) > 1) {
                $extensions = [];

                foreach ($contentElementsPaths as $contentElementsPath) {
                    $this->classPath = $contentElementsPath;
                    $extensions[] = substr(explode('/', $contentElementsPath)[0], 4);
                }

                $this->extension = $this->questionHelper->ask($this->input, $this->output, new ChoiceQuestion(
                    'Please select extension.',
                    $extensions,
                    0
                ));
            } else {
                $this->classPath = $contentElementsPaths[0];
                $this->extension = substr(explode('/', $this->classPath)[0], 4);
            }
        }
        if (u($this->classPath)->endsWith('/') === false) {
            $this->classPath = $this->classPath . '/';
        }

        if ($this->typo3ElementRegistryCliConfig['contentElement']['classTemplatePath']) {
            $this->classTemplatePath = $this->typo3ElementRegistryCliConfig['classTemplatePath'];
        }
        if ($this->typo3ElementRegistryCliConfig['contentElement']['modelTemplatePath']) {
            $this->modelTemplatePath = $this->typo3ElementRegistryCliConfig['contentElement']['modelTemplatePath'];
        }
        if ($this->typo3ElementRegistryCliConfig['contentElement']['templateTemplatePath']) {
            $this->templateTemplatePath =
                $this->typo3ElementRegistryCliConfig['contentElement']['templateTemplatePath'];
        }
        $this->classTemplatePath = GeneralUtility::getFileAbsFileName($this->classTemplatePath);
        $this->modelTemplatePath = GeneralUtility::getFileAbsFileName($this->modelTemplatePath);
        $this->templateTemplatePath = GeneralUtility::getFileAbsFileName($this->templateTemplatePath);

        if ($this->typo3ElementRegistryCliConfig['contentElement']['classExtend']) {
            $this->classExtend = $this->typo3ElementRegistryCliConfig['contentElement']['classExtend'];
        }

        if ($this->typo3ElementRegistryCliConfig['contentElement']['modelExtend']) {
            $this->modelExtend = $this->typo3ElementRegistryCliConfig['contentElement']['modelExtend'];
        }

        $this->contentElementObject = (new ContentElementObject(
            $this->input,
            $this->output,
            $this->questionHelper,
            $this
        ));
        $this->contentElementObject->questions();

        $this->classNamespace = $this->vendor . '\\' . u($this->extension)->camel()->title(true)
            . '\\ContentElement';

        $this->modelNamespace = $this->vendor . '\\' . u($this->extension)->camel()->title(true) . '\\'
        . 'Domain\\Model\\ContentElement';

        /** Init class path */
        $this->classPath = GeneralUtility::getFileAbsFileName(
            $this->classPath . $this->contentElementObject->getName() . '.php'
        );

        /** Init model path */
        $this->modelPath = GeneralUtility::getFileAbsFileName(
            'EXT:' . $this->extension . '/Classes/Domain/Model/ContentElement/' .
            $this->contentElementObject->getName() . '.php'
        );

        /** Init template path */
        $this->templatePath = GeneralUtility::getFileAbsFileName(
            'EXT:' . $this->extension . '/Resources/Private/Templates/ContentElements/' .
            $this->contentElementObject->getName() . '.html'
        );

        parent::beforeMake();
    }

    /**
     * @return void
     */
    public function make(): void
    {
        $elementId = str_replace('_', '', $this->extension) . '_' . strtolower($this->contentElementObject->getName());

        /** Class */
        $classTemplate = file_get_contents($this->classTemplatePath);
        $classTemplate = str_replace([
            '{contentElementName}', '{extend}', '{namespace}'
        ], [
            $this->contentElementObject->getName(),
            $this->classExtend,
            $this->classNamespace
        ], $classTemplate);
        FileUtility::createFile($this->classPath, $classTemplate);

        /** Model */
        $modelTemplate = file_get_contents($this->modelTemplatePath);
        $modelTemplate = str_replace([
            '{contentElementName}', '{extend}', '{namespace}'
        ], [
            $this->contentElementObject->getName(),
            $this->modelExtend,
            $this->modelNamespace
        ], $modelTemplate);
        FileUtility::createFile($this->modelPath, $modelTemplate);

        /** Template */
        FileUtility::createFile($this->templatePath, file_get_contents($this->templateTemplatePath));

        /** Write title and description to locallang */
        TranslationUtility::addStringToTranslation(
            'EXT:' . $this->extension . '/Resources/Private/Language/locallang_db.xlf',
            $this->table . '.' . $elementId . '.title',
            $this->contentElementObject->getTitle()
        );
        TranslationUtility::addStringToTranslation(
            'EXT:' . $this->extension . '/Resources/Private/Language/locallang_db.xlf',
            $this->table . '.' . $elementId . '.description',
            $this->contentElementObject->getDescription()
        );

        /** Copy icon and preview image */
        ImageUtility::copyIcon(
            'EXT:' . $this->extension . '/Resources/Public/Icons/ContentElement',
            $elementId
        );
        ImageUtility::copyImage(
            'EXT:' . $this->extension . '/Resources/Public/Images/ContentElementPreviews',
            'common_' . $elementId
        );
    }

    /**
     * @return void
     */
    public function afterMake(): void
    {
        $this->output->writeln('<bg=red;options=bold>Change content element icon</>');
        $this->output->writeln('<bg=red;options=bold>Change content element preview image</>');
        parent::afterMake();
    }
}
