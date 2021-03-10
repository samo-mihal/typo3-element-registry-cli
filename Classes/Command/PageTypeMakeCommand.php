<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command;

use Digitalwerk\Typo3ElementRegistryCli\ElementObjects\PageTypeObject;
use Digitalwerk\Typo3ElementRegistryCli\Utility\RegisterPageTypeUtility;
use Digitalwerk\Typo3ElementRegistryCli\Utility\TranslationUtility;
use Digitalwerk\Typo3ElementRegistryCli\Utility\TyposcriptUtility;
use Symfony\Component\Console\Question\ChoiceQuestion;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class PageTypeMakeCommand
 * @package Digitalwerk\Typo3ElementRegistryCli\Command
 */
class PageTypeMakeCommand extends AbstractMakeCommand
{
    /**
     * Default constants
     */
    const DEFAULT_TYPOSCRIPT_PATH =
        'EXT:typo3_element_registry_cli/Resources/Private/Templates/PageType/Typoscript.txt';
    const DEFAULT_MODEL_PATH =
        'EXT:typo3_element_registry_cli/Resources/Private/Templates/PageType/Model.txt';
    const DEFAULT_MODEL_EXTEND =
        '\Digitalwerk\ContentElementRegistry\Domain\Model\ContentElement';
    const DEFAULT_MODEL_NAMESPACE =
        'Vendor\Extension\Domain\Model';

    /**
     * @var array
     */
    protected $requiredFiles = [
        'EXT:{extension}/ext_tables.php',
        'EXT:{extension}/Configuration/TCA/Overrides/pages.php',
        'EXT:{extension}/ext_typoscript_setup.typoscript',
        'EXT:{extension}/Resources/Private/Language/locallang_db.xlf'
    ];

    /**
     * @var string
     */
    public $table = 'pages';

    /**
     * @var PageTypeObject
     */
    protected $pageTypeObject = null;

    /**
     * @var string
     */
    protected $modelPath = '';

    /**
     * @var string
     */
    protected $modelTemplatePath = self::DEFAULT_MODEL_PATH;

    /**
     * @var string
     */
    protected $typoscriptTemplatePath = self::DEFAULT_TYPOSCRIPT_PATH;

    /**
     * @var string
     */
    protected $modelExtend = self::DEFAULT_MODEL_EXTEND;

    /**
     * @var string
     */
    public $modelNamespace = self::DEFAULT_MODEL_NAMESPACE;

    /**
     * @var string
     */
    protected $typoScriptConstantsPath = '';

    /**
     * @var string
     */
    protected $utilityPath = '';

    /**
     * @return void
     */
    public function beforeMake(): void
    {
        $this->extension = $this->questionHelper->ask(
            $this->input,
            $this->output,
            (new ChoiceQuestion(
                'Page type extension: ',
                array_keys($GLOBALS['TYPO3_LOADED_EXT'])
            )
            )
        );

        $this->typoScriptConstantsPath = GeneralUtility::getFileAbsFileName(
            $this->typo3ElementRegistryCliConfig['pageType']['typoScriptConstantsPath']
        );
        if (empty($this->typoScriptConstantsPath)) {
            throw new \InvalidArgumentException('Typoscript constants path cannot be empty.');
        }

        $this->utilityPath = $this->typo3ElementRegistryCliConfig['pageType']['utilityPath'];
        if (empty($this->utilityPath)) {
            throw new \InvalidArgumentException('Utility path cannot be empty.');
        }

        if ($this->typo3ElementRegistryCliConfig['pageType']['modelTemplatePath']) {
            $this->modelTemplatePath = $this->typo3ElementRegistryCliConfig['contentElement']['modelTemplatePath'];
        }
        $this->modelTemplatePath = GeneralUtility::getFileAbsFileName($this->modelTemplatePath);

        if ($this->typo3ElementRegistryCliConfig['pageType']['modelExtend']) {
            $this->modelExtend = $this->typo3ElementRegistryCliConfig['pageType']['modelExtend'];
        }

        if ($this->typo3ElementRegistryCliConfig['pageType']['modelNamespace']) {
            $this->modelNamespace = $this->typo3ElementRegistryCliConfig['pageType']['modelNamespace'];
        }

        $this->pageTypeObject = (new PageTypeObject($this->input, $this->output, $this->questionHelper, $this));
        $this->pageTypeObject->questions();

        /** Init model path */
        $this->modelPath = GeneralUtility::getFileAbsFileName(
            'EXT:' . $this->extension . '/Classes/Domain/Model/' . $this->pageTypeObject->getName() . '.php'
        );

        parent::beforeMake();
    }

    /**
     * @return void
     */
    public function make(): void
    {
        $upperCaseName = strtoupper($this->pageTypeObject->getName());

        /** Write title to locallang */
        TranslationUtility::addStringToTranslation(
            'EXT:' . $this->extension . '/Resources/Private/Language/locallang_db.xlf',
            'page.type.' . $this->pageTypeObject->getDoktype() . '.label',
            $this->pageTypeObject->getTitle()
        );

        /** Copy icons */
        copy(
            GeneralUtility::getFileAbsFileName('EXT:content_element_registry/Resources/Public/Icons/CEDefaultIcon.svg'),
            GeneralUtility::getFileAbsFileName(
                'EXT:' . $this->extension . '/Resources/Public/Icons/dw-page-type-' .
                $this->pageTypeObject->getDoktype() . '.svg'
            )
        );
        copy(
            GeneralUtility::getFileAbsFileName('EXT:content_element_registry/Resources/Public/Icons/CEDefaultIcon.svg'),
            GeneralUtility::getFileAbsFileName(
                'EXT:' . $this->extension . '/Resources/Public/Icons/dw-page-type-' .
                $this->pageTypeObject->getDoktype() . '-not-in-menu.svg'
            )
        );

        /** Model */
        $modelTemplate = file_get_contents($this->modelTemplatePath);
        $modelTemplate = str_replace([
            '{pageTypeName}', '{extend}', '{namespace}', '{doktype}'
        ], [
            $this->pageTypeObject->getName(),
            $this->modelExtend,
            $this->modelNamespace,
            $this->pageTypeObject->getDoktype()
        ], $modelTemplate);
        file_put_contents($this->modelPath, $modelTemplate);

        /** Add typoscript to extbase persistence classes */
        $requiredTyposcript = file_get_contents(GeneralUtility::getFileAbsFileName($this->typoscriptTemplatePath));
        $requiredTyposcript = str_replace([
            '{namespace}', '{table}', '{name}', '{nameUpperCase}'
        ], [
            $this->modelNamespace,
            $this->table,
            $this->pageTypeObject->getName(),
            $upperCaseName
        ], $requiredTyposcript);
        TyposcriptUtility::addToExtbasePersistenceClasses(
            'EXT:' . $this->extension . '/ext_typoscript_setup.typoscript',
            $requiredTyposcript,
            $this->output
        );

        /** Add typoscript constant */
        TyposcriptUtility::addToConstants(
            $this->typoScriptConstantsPath,
            'PAGE_DOKTYPE_' . $upperCaseName . ' = ' . $this->pageTypeObject->getDoktype(),
            $this->output
        );

        /** Register doktype */
        $registerDoktypeWithUtility = $this->utilityPath .
            '::addPageDoktype(\\' . $this->modelNamespace . '\\' .
            $this->pageTypeObject->getName() . '::getDoktype());';
        RegisterPageTypeUtility::registerDoktype(
            'EXT:' . $this->extension . '/ext_tables.php',
            $registerDoktypeWithUtility,
            $this->output
        );

        /** Register TCA doktype */
        $registerTCADoktypeWithUtility = $this->utilityPath .
            '::addTcaDoktype(\\' . $this->modelNamespace . '\\' . $this->pageTypeObject->getName() . '::getDoktype());';
        RegisterPageTypeUtility::registerTCADoktype(
            'EXT:' . $this->extension . '/Configuration/TCA/Overrides/pages.php',
            $registerTCADoktypeWithUtility,
            $this->output
        );
    }

    /**
     * @return void
     */
    public function afterMake(): void
    {
        $this->output->writeln('<bg=red;options=bold>Change page type icons</>');
        parent::afterMake();
    }
}
