<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command;

use Digitalwerk\Typo3ElementRegistryCli\ElementObjects\PageTypeObject;
use Digitalwerk\Typo3ElementRegistryCli\Utility\ExtbaseUtility;
use Digitalwerk\Typo3ElementRegistryCli\Utility\ExtensionUtility;
use Digitalwerk\Typo3ElementRegistryCli\Utility\FileUtility;
use Digitalwerk\Typo3ElementRegistryCli\Utility\ImageUtility;
use Digitalwerk\Typo3ElementRegistryCli\Utility\RegisterPageTypeUtility;
use Digitalwerk\Typo3ElementRegistryCli\Utility\TranslationUtility;
use Symfony\Component\Console\Question\ChoiceQuestion;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use function Symfony\Component\String\u;

/**
 * Class PageTypeMakeCommand
 * @package Digitalwerk\Typo3ElementRegistryCli\Command
 */
class PageTypeMakeCommand extends AbstractMakeCommand
{
    const DEFAULT_MODEL_PATH =
        'EXT:typo3_element_registry_cli/Resources/Private/Templates/PageType/Model.txt';
    const DEFAULT_MODEL_EXTEND =
        'TYPO3\CMS\Extbase\DomainObject\AbstractEntity';

    /**
     * @var array
     */
    protected $requiredFiles = [
        'EXT:{extension}/ext_tables.php',
        'EXT:{extension}/Resources/Private/Language/locallang_db.xlf',
        'EXT:{extension}/Configuration/Extbase/Persistence/Classes.php'
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
    protected $modelExtend = self::DEFAULT_MODEL_EXTEND;

    /**
     * @var string
     */
    public $modelNamespace = '';

    /**
     * @var string
     */
    protected $utility = '';

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
                ExtensionUtility::getActiveExtensions()
            )
            )
        );

        $this->utility = $this->typo3ElementRegistryCliConfig['pageType']['utilityPath'];
        if (empty($this->utility)) {
            throw new \InvalidArgumentException('Utility path cannot be empty.');
        }
        $this->utility = (new $this->utility);

        if ($this->typo3ElementRegistryCliConfig['pageType']['modelTemplatePath']) {
            $this->modelTemplatePath = $this->typo3ElementRegistryCliConfig['contentElement']['modelTemplatePath'];
        }
        $this->modelTemplatePath = GeneralUtility::getFileAbsFileName($this->modelTemplatePath);

        if ($this->typo3ElementRegistryCliConfig['pageType']['modelExtend']) {
            $this->modelExtend = $this->typo3ElementRegistryCliConfig['pageType']['modelExtend'];
        }

        $this->pageTypeObject = (new PageTypeObject($this->input, $this->output, $this->questionHelper, $this));
        $this->pageTypeObject->questions();

        $this->modelNamespace =  $this->vendor . '\\' . u($this->extension)->camel()->title(true) . '\\'
            . 'Domain\\Model';

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
        /** Write title to locallang */
        TranslationUtility::addStringToTranslation(
            'EXT:' . $this->extension . '/Resources/Private/Language/locallang_db.xlf',
            'page.type.' . $this->pageTypeObject->getDoktype() . '.label',
            $this->pageTypeObject->getTitle()
        );

        /** Copy icons */
        ImageUtility::copyIcon(
            'EXT:' . $this->extension . '/Resources/Public/Icons',
            $this->utility->getDoktypeIconIdentifier(
                $this->pageTypeObject->getDoktype()
            )
        );
        ImageUtility::copyIcon(
            'EXT:' . $this->extension . '/Resources/Public/Icons',
            $this->utility->getDoktypeIconIdentifier(
                $this->pageTypeObject->getDoktype()
            ) . '-not-in-menu'
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
        FileUtility::createFile($this->modelPath, $modelTemplate);

        /** Add extbase persistence classes */
        ExtbaseUtility::addToExtbasePersistenceClasses(
            'EXT:' . $this->extension . '/Configuration/Extbase/Persistence/Classes.php',
            $this->modelNamespace . '\\' . $this->pageTypeObject->getName(),
            $this->output
        );

        /** Add doktype */
        $registerDoktypeWithUtility = '\\' . get_class($this->utility) .
            '::addPageDoktype(\\' . $this->modelNamespace . '\\' .
            $this->pageTypeObject->getName() . '::getDoktype());';
        RegisterPageTypeUtility::addDoktype(
            'EXT:' . $this->extension . '/ext_tables.php',
            $registerDoktypeWithUtility,
            $this->output
        );

        /** Generate TCA */
        $tca = file_get_contents(
            GeneralUtility::getFileAbsFileName(
                'EXT:typo3_element_registry_cli/Resources/Private/Templates/PageType/TCA.txt'
            )
        );
        $tca = str_replace(['{name}', '{class}', '{utilityClass}', '{extension}', '{doktype}'], [
            lcfirst($this->pageTypeObject->getName()),
            $this->modelNamespace . '\\' . $this->pageTypeObject->getName(),
            get_class($this->utility),
            $this->extension,
            $this->pageTypeObject->getDoktype()
        ], $tca);
        ExtbaseUtility::addPageTCA(
            GeneralUtility::getFileAbsFileName(
                'EXT:' . $this->extension . '/Configuration/TCA/Overrides/pages_' .
                u($this->pageTypeObject->getName())->lower()->camel() . '.php'
            ),
            $tca
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
