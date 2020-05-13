<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\FieldsObject;
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
use Digitalwerk\Typo3ElementRegistryCli\Command\RunCreateElementCommand;
use InvalidArgumentException;
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
    protected $contentElementModelExtendClass = 'Digitalwerk\ContentElementRegistry\Domain\Model\ContentElement';

    /**
     * @var string
     */
    protected $contentElementInlineModelExtendClass = 'Digitalwerk\ContentElementRegistry\Domain\Model\Relation';

    /**
     * @var string
     */
    protected $pageTypeInlineModelExtendClass = 'TYPO3\CMS\Extbase\DomainObject\AbstractEntity';

    /**
     * @var string
     */
    protected $iconRegisterClass = 'Digitalwerk\ContentElementRegistry\Utility\ContentElementRegistryUtility';

    /**
     * @var string
     */
    protected $recordModelExtendClass = 'TYPO3\CMS\Extbase\DomainObject\AbstractEntity';

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
    protected $registerPageDoktypeClass = 'Digitalwerk\Typo3ElementRegistryCli\Utility\Typo3ElementRegistryCliUtility';

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
    public function getTcaRelativePath(): string
    {
        $tcaRelativePath = explode('/', $this->getInlineRelativePath());

        $iterator = 0;
        foreach ($tcaRelativePath as $tcaRelativePathItem) {
            if ($tcaRelativePathItem === $this->getStaticName()) {
                break;
            }
            $iterator++;
        }

        $tcaRelativePath = array_slice($tcaRelativePath, $iterator);
        $tcaRelativePath = implode('_', $tcaRelativePath);
        $tcaRelativePath = $tcaRelativePath ? $tcaRelativePath . '_' . $this->getName(): $this->getName();
        return strtolower($tcaRelativePath);
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
     * @return bool
     */
    public function isTcaFieldsPrefix(): bool
    {
        return $this->tcaFieldsPrefix;
    }

    /**
     * @param bool|null $tcaFieldsPrefix
     */
    public function setTcaFieldsPrefix(? bool $tcaFieldsPrefix): void
    {
        $this->tcaFieldsPrefix = $tcaFieldsPrefix;
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
     * @param string|null $betweenProtectedsAndGetters
     */
    public function setBetweenProtectedsAndGetters(? string $betweenProtectedsAndGetters): void
    {
        $this->betweenProtectedsAndGetters = $betweenProtectedsAndGetters;
    }

    /**
     * @return RunCreateElementCommand|object
     */
    public function getRunCreateCommand()
    {
        return GeneralUtility::makeInstance(RunCreateElementCommand::class);
    }

    /**
     * @return mixed
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getCreateCommandOverrideClasses()
    {
         return $this->getCreateCommandCustomData()->overrideClasses();
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getIconRegisterClass(): string
    {
        $overrideIconRegisterClass = $this->getCreateCommandOverrideClasses()['iconRegisterClass'];
        return $overrideIconRegisterClass ? $overrideIconRegisterClass : $this->iconRegisterClass;
    }

    /**
     * @return object
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getCreateCommandCustomData(){
        $mainExtension = $this->getRunCreateCommand()->getMainExtensionInNameSpaceFormat();
        $vendor = $this->getRunCreateCommand()->getVendor();

        return GeneralUtility::makeInstance($vendor . "\\" . $mainExtension . "\\CreateCommandConfig\CreateCommandCustomData");
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getContentElementInlineModelExtendClass(): string
    {
        $overrideContentElementInlineModelExtendClass = $this->getCreateCommandOverrideClasses()['contentElementInlineModelExtendClass'];
        return $overrideContentElementInlineModelExtendClass ? $overrideContentElementInlineModelExtendClass : $this->contentElementInlineModelExtendClass;
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getPageTypeInlineModelExtendClass(): string
    {
        $overridePageTypeInlineModelExtendClass = $this->getCreateCommandOverrideClasses()['pageTypeInlineModelExtendClass'];
        return $overridePageTypeInlineModelExtendClass ? $overridePageTypeInlineModelExtendClass : $this->pageTypeInlineModelExtendClass;
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getInlineModelExtendClass(): string
    {
        if ($this->getExtensionName() === $this->getRunCreateCommand()->getMainExtension()) {
            return $this->getContentElementInlineModelExtendClass();
        }

        return $this->getPageTypeInlineModelExtendClass();
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getContentElementModelExtendClass(): string
    {
        $overrideDefaultModelExtendClass = $this->getCreateCommandOverrideClasses()['contentElementExtendClass'];

        return $overrideDefaultModelExtendClass ? $overrideDefaultModelExtendClass : $this->contentElementModelExtendClass;
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getRecordModelExtendClass(): string
    {
        $overrideRecordModelExtendClass = $this->getCreateCommandOverrideClasses()['recordModelExtendClass'];

        return $overrideRecordModelExtendClass ? $overrideRecordModelExtendClass : $this->recordModelExtendClass;
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getPageTypeModelExtendClass(): string
    {
        $overridePageTypeModelExtendClass = $this->getCreateCommandOverrideClasses()['pageTypeModelExtendClass'];

        return $overridePageTypeModelExtendClass ? $overridePageTypeModelExtendClass : $this->pageTypeModelExtendClass;
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getRegisterPageDoktypeClass(): string
    {
        $overrideRegisterPageDoktypeClass = $this->getCreateCommandOverrideClasses()['registerPageDoktypeClass'];
        return $overrideRegisterPageDoktypeClass ? $overrideRegisterPageDoktypeClass : $this->registerPageDoktypeClass;
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
        $pathToTypoScriptConstants = $this->getCreateCommandCustomData()->pathToTypoScriptConstants();

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
        $overridePluginControllerExtendClass = $this->getCreateCommandOverrideClasses()['pluginControllerExtendClass'];

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
     * @return string
     */
    public function getExtensionNameSpaceFormat(): string
    {
        return str_replace(' ','',ucwords(str_replace('_',' ', $this->extensionName)));
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

    /**
     * @return string
     */
    public function getTranslationPathShort(): string
    {
        return 'LLL:EXT:' . $this->getExtensionName() . '/Resources/Private/Language/locallang_db.xlf';
    }

    /**
     * @return string
     */
    public function getTranslationPathFromRoot(): string
    {
        return 'public/typo3conf/ext/' . $this->getExtensionName() . '/Resources/Private/Language/locallang_db.xlf';
    }
}
