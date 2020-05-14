<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\CheckRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\ContentElementClassRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\ControllerRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\FlexFormRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\IconRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\InlineRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\ModelRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\PreviewImageRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\RegisterRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\SQLDatabaseRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\TCARender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\TemplateRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\TranslationRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\Typo3CmsRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender\TypoScriptRender;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ElementRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render
 */
class ElementRender
{
    /**
     * @var \Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject
     */
    protected $element = null;

    /**
     * @var string
     */
    protected $vendor = '';

    /**
     * @var string
     */
    protected $mainExtension = '';


    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $controllerName = '';

    /**
     * @var bool
     */
    protected $autoHeader = false;

    /**
     * @var OutputInterface
     */
    protected $output = null;

    /**
     * @var InputInterface
     */
    protected $input = null;

    /**
     * @var bool
     */
    protected $tcaFieldsPrefix = true;

    /**
     * @var string
     */
    protected $staticName = '';

    /**
     * @var string
     */
    protected $table = '';

    /**
     * @var string
     */
    protected $relativePathToClass = '';

    /**
     * @var string
     */
    protected $extensionName = '';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $inlineRelativePath = '';

    /**
     * @var array
     */
    protected $inlineFields = [];

    /**
     * @var string
     */
    protected $betweenProtectedsAndGetters = '';

    /**
     * @var string
     */
    protected $modelNamespace = '';

    /**
     * @var string
     */
    protected $actionName = '';

    /**
     * @var int
     */
    protected $doktype = 0;

    /**
     * @return ElementObject
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @param ElementObject $element
     */
    public function setElement($element)
    {
        $this->element = $element;
    }

    /**
     * @param string $vendor
     */
    public function setVendor(string $vendor): void
    {
        $this->vendor = $vendor;
    }

    /**
     * @param string $mainExtension
     */
    public function setMainExtension(string $mainExtension): void
    {
        $this->mainExtension = $mainExtension;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @param bool|null $autoHeader
     */
    public function setAutoHeader(? bool $autoHeader): void
    {
        $this->autoHeader = $autoHeader;
    }

    /**
     * @param string $relativePathToClass
     */
    public function setRelativePathToClass(string $relativePathToClass): void
    {
        $this->relativePathToClass = $relativePathToClass;
    }

    /**
     * @param string|null $actionName
     */
    public function setActionName(? string $actionName): void
    {
        $this->actionName = $actionName;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $staticName
     */
    public function setStaticName(string $staticName)
    {
        $this->staticName = $staticName;
    }

    /**
     * @param string|null $controllerName
     */
    public function setControllerName(? string $controllerName): void
    {
        $this->controllerName = $controllerName;
    }

    /**
     * @param string $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @param string $extensionName
     */
    public function setExtensionName($extensionName)
    {
        $this->extensionName = $extensionName;
    }

    /**
     * @param int|null $doktype
     */
    public function setDoktype(? int $doktype): void
    {
        $this->doktype = $doktype;
    }

    /**
     * @param string $modelNamespace
     */
    public function setModelNamespace(string $modelNamespace)
    {
        $this->modelNamespace = $modelNamespace;
    }

    /**
     * @param string $inlineRelativePath
     */
    public function setInlineRelativePath($inlineRelativePath)
    {
        $this->inlineRelativePath = $inlineRelativePath;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(? string $title): void
    {
        $this->title = $title;
    }

    /**
     * @param bool|null $tcaFieldsPrefix
     */
    public function setTcaFieldsPrefix(? bool $tcaFieldsPrefix): void
    {
        $this->tcaFieldsPrefix = $tcaFieldsPrefix;
    }

    /**
     * @param string|null $betweenProtectedsAndGetters
     */
    public function setBetweenProtectedsAndGetters(? string $betweenProtectedsAndGetters): void
    {
        $this->betweenProtectedsAndGetters = $betweenProtectedsAndGetters;
    }

    /**
     * @param array|null $inlineFields
     */
    public function setInlineFields(? array $inlineFields)
    {
        $this->inlineFields = $inlineFields;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    /**
     * @param InputInterface $input
     */
    public function setInput(InputInterface $input): void
    {
        $this->input = $input;
    }

    /**
     * @return ContentElementClassRender
     */
    public function contentElementClass()
    {
        return GeneralUtility::makeInstance(ContentElementClassRender::class, $this);
    }

    /**
     * @return ModelRender
     */
    public function model()
    {
        return GeneralUtility::makeInstance(ModelRender::class, $this);
    }

    /**
     * @return TemplateRender
     */
    public function template()
    {
        return GeneralUtility::makeInstance(TemplateRender::class, $this);
    }

    /**
     * @return TCARender
     */
    public function tca()
    {
        return GeneralUtility::makeInstance(TCARender::class, $this);
    }

    /**
     * @return IconRender
     */
    public function icon()
    {
        return GeneralUtility::makeInstance(IconRender::class, $this);
    }

    /**
     * @return PreviewImageRender
     */
    public function previewImage()
    {
        return GeneralUtility::makeInstance(PreviewImageRender::class, $this);
    }

    /**
     * @return InlineRender
     */
    public function inline()
    {
        return GeneralUtility::makeInstance(InlineRender::class, $this);
    }

    /**
     * @return TypoScriptRender
     */
    public function typoScript()
    {
        return GeneralUtility::makeInstance(TypoScriptRender::class, $this);
    }

    /**
     * @return SQLDatabaseRender
     */
    public function sqlDatabase()
    {
        return GeneralUtility::makeInstance(SQLDatabaseRender::class, $this);
    }

    /**
     * @return TranslationRender
     */
    public function translation()
    {
        return GeneralUtility::makeInstance(TranslationRender::class, $this);
    }

    /**
     * @return FlexFormRender
     */
    public function flexForm()
    {
        return GeneralUtility::makeInstance(FlexFormRender::class, $this);
    }

    /**
     * @return RegisterRender
     */
    public function register()
    {
        return GeneralUtility::makeInstance(RegisterRender::class, $this);
    }

    /**
     * @return ControllerRender
     */
    public function controller()
    {
        return GeneralUtility::makeInstance(ControllerRender::class, $this);
    }

    /**
     * @return CheckRender
     */
    public function check()
    {
        return GeneralUtility::makeInstance(CheckRender::class, $this);
    }

    /**
     * @return Typo3CmsRender
     */
    public function typo3Cms()
    {
        return GeneralUtility::makeInstance(Typo3CmsRender::class, $this);
    }
}
