<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\FieldsObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\CheckRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ContentElementClassRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ControllerRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\FlexFormRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\IconRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\InlineRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ModelRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\RegisterRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\TCARender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\PreviewImageRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\SQLDatabaseRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\TemplateRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\TranslationRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\TypoScriptRender;
use Digitalwerk\Typo3ElementRegistryCli\Command\RunCreateElementCommand;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Render
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand
 */
class RenderCreateCommand
{
    /**
     * @var \Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\FieldsObject
     */
    protected $fields = null;

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
    protected $contentElementAndInlineModelExtendClass = 'Digitalwerk\ContentElementRegistry\Domain\Model\ContentElement';

    /**
     * @var string
     */
    protected $pageTypeModelExtendClass = 'TYPO3\CMS\Extbase\DomainObject\AbstractEntity';

    /**
     * @var string
     */
    protected $pluginControllerExtendClass = 'TYPO3\CMS\Extbase\Mvc\Controller\ActionController';

    /**
     * @var string
     */
    protected $elementType = '';

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
     * @var null
     */
    protected $optionalClass = null;

    /**
     * @var OutputInterface
     */
    protected $output = null;

    /**
     * @var InputInterface
     */
    protected $input = null;

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
    protected $pathToTypoScriptConstants = '';

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
     * @return FieldsObject
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param FieldsObject $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return string
     */
    public function getVendor(): string
    {
        return $this->vendor;
    }

    /**
     * @param string $vendor
     */
    public function setVendor(string $vendor): void
    {
        $this->vendor = $vendor;
    }

    /**
     * @return string
     */
    public function getMainExtension(): string
    {
        return $this->mainExtension;
    }

    /**
     * @param string $mainExtension
     */
    public function setMainExtension(string $mainExtension): void
    {
        $this->mainExtension = $mainExtension;
    }

    /**
     * @return string
     */
    public function getElementType(): string
    {
        return $this->elementType;
    }

    /**
     * @param string $elementType
     */
    public function setElementType(string $elementType): void
    {
        $this->elementType = $elementType;
    }

    /**
     * @return bool
     */
    public function isAutoHeader(): bool
    {
        return $this->autoHeader;
    }

    /**
     * @param bool|null $autoHeader
     */
    public function setAutoHeader(? bool $autoHeader): void
    {
        $this->autoHeader = $autoHeader;
    }

    /**
     * @return string
     */
    public function getRelativePathToClass(): string
    {
        return $this->relativePathToClass;
    }

    /**
     * @param string $relativePathToClass
     */
    public function setRelativePathToClass(string $relativePathToClass): void
    {
        $this->relativePathToClass = $relativePathToClass;
    }

    /**
     * @return string
     */
    public function getActionName(): string
    {
        return $this->actionName;
    }

    /**
     * @param string|null $actionName
     */
    public function setActionName(? string $actionName): void
    {
        $this->actionName = $actionName;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getStaticName(): string
    {
        return $this->staticName;
    }

    /**
     * @param string $staticName
     */
    public function setStaticName(string $staticName)
    {
        $this->staticName = $staticName;
    }

    /**
     * @return string
     */
    public function getControllerName(): string
    {
        return $this->controllerName;
    }

    /**
     * @param string|null $controllerName
     */
    public function setControllerName(? string $controllerName): void
    {
        $this->controllerName = $controllerName;
    }

    /**
     * @return string
     */
    public function getExtensionName()
    {
        return $this->extensionName;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
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
     * @return int|null
     */
    public function getDoktype(): ? int
    {
        return $this->doktype;
    }

    /**
     * @param int|null $doktype
     */
    public function setDoktype(? int $doktype): void
    {
        $this->doktype = $doktype;
    }

    /**
     * @return string
     */
    public function getInlineRelativePath()
    {
        return $this->inlineRelativePath;
    }

    /**
     * @return string
     */
    public function getModelNamespace(): string
    {
        return $this->modelNamespace;
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
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(? string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return null
     */
    public function getOptionalClass()
    {
        return $this->optionalClass;
    }

    /**
     * @return string
     */
    public function getBetweenProtectedsAndGetters()
    {
        return $this->betweenProtectedsAndGetters;
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getContentElementAndInlineModelExtendClass(): string
    {
        $mainExtension = GeneralUtility::makeInstance(RunCreateElementCommand::class)->getMainExtensionInNameSpaceFormat();
        $vendor = GeneralUtility::makeInstance(RunCreateElementCommand::class)->getVendor();

        $createCommandCustomData = GeneralUtility::makeInstance($vendor . "\\" . $mainExtension . "\\CreateCommandConfig\CreateCommandCustomData");
        $overrideContentElementAndInlineModelExtendClass = $createCommandCustomData->overrideContentElementAndInlineModelExtendClass();

        return $overrideContentElementAndInlineModelExtendClass ? $overrideContentElementAndInlineModelExtendClass : $this->contentElementAndInlineModelExtendClass;
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getPageTypeModelExtendClass(): string
    {
        $mainExtension = GeneralUtility::makeInstance(RunCreateElementCommand::class)->getMainExtensionInNameSpaceFormat();
        $vendor = GeneralUtility::makeInstance(RunCreateElementCommand::class)->getVendor();

        $createCommandCustomData = GeneralUtility::makeInstance($vendor . "\\" . $mainExtension . "\\CreateCommandConfig\CreateCommandCustomData");
        $overridePageTypeModelExtendClass = $createCommandCustomData->overridePageTypeModelExtendClass();

        return $overridePageTypeModelExtendClass ? $overridePageTypeModelExtendClass : $this->pageTypeModelExtendClass;
    }

    /**
     * @return array|null
     */
    public function getInlineFields(): ? array
    {
        return $this->inlineFields;
    }

    /**
     * @param array|null $inlineFields
     */
    public function setInlineFields(? array $inlineFields)
    {
        $this->inlineFields = $inlineFields;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getPathToTypoScriptConstants(): string
    {
        $mainExtension = GeneralUtility::makeInstance(RunCreateElementCommand::class)->getMainExtensionInNameSpaceFormat();
        $vendor = GeneralUtility::makeInstance(RunCreateElementCommand::class)->getVendor();

        $createCommandCustomData = GeneralUtility::makeInstance($vendor . "\\" . $mainExtension . "\\CreateCommandConfig\CreateCommandCustomData");
        $pathToTypoScriptConstants = $createCommandCustomData->pathToTypoScriptConstants();

        if (empty($pathToTypoScriptConstants)) {
            throw new InvalidArgumentException('Path to TypoScript model can not be empty.');
        }
        return $pathToTypoScriptConstants;
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getPluginControllerExtendClass(): string
    {
        $mainExtension = GeneralUtility::makeInstance(RunCreateElementCommand::class)->getMainExtensionInNameSpaceFormat();
        $vendor = GeneralUtility::makeInstance(RunCreateElementCommand::class)->getVendor();

        $createCommandCustomData = GeneralUtility::makeInstance($vendor . "\\" . $mainExtension . "\\CreateCommandConfig\CreateCommandCustomData");
        $overridePluginControllerExtendClass = $createCommandCustomData->overridePluginControllerExtendClass();

        return $overridePluginControllerExtendClass ? $overridePluginControllerExtendClass : $this->pluginControllerExtendClass;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
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
     * @param $extensionName
     * @return string
     */
    public function getExtensionNameSpaceFormat($extensionName): string
    {
        return str_replace(' ','',ucwords(str_replace('_',' ',$extensionName)));
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
}
