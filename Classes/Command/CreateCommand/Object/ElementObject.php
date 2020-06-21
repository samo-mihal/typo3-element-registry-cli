<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\ElementSetup;
use Digitalwerk\Typo3ElementRegistryCli\Utility\FieldsCreateCommandUtility;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class ElementObject
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object
 */
class ElementObject
{
    const FIELDS_TAB = '    ';

    /**
     * @var ObjectStorage<\Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\FieldObject>
     */
    protected $fields = null;

    /**
     * @var string
     */
    protected $staticType = '';

    /**
     * @var string
     */
    protected $description = '';

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
    protected $registerPluginFlexFormClass = 'Digitalwerk\Typo3ElementRegistryCli\Utility\Typo3ElementRegistryCliUtility';

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
    protected $extensionName = '';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $modelDirPath = '';

    /**
     * @var array
     */
    protected $inlineFields = [];

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
     * @var string
     */
    protected $fieldsSpacesInTcaColumn = '    ';

    /**
     * @var string
     */
    protected $fieldsSpacesInTypoScriptMapping = '            ';

    /**
     * @var string
     */
    protected $fieldsSpacesInTcaPalette = '            ';

    /**
     * @var string
     */
    protected $fieldsSpacesInTcaColumnsOverrides = '            ';

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getFieldsSpacesInTypoScriptMapping(): string
    {
        return $this->fieldsSpacesInTypoScriptMapping;
    }

    /**
     * @return string
     */
    public function getFieldsSpacesInTcaPalette(): string
    {
        return $this->fieldsSpacesInTcaPalette;
    }

    /**
     * @return string
     */
    public function getFieldsSpacesInTcaColumnsOverrides(): string
    {
        return $this->fieldsSpacesInTcaColumnsOverrides;
    }

    /**
     * @return string
     */
    public function getFieldsSpacesInTcaColumnsOverridesConfig(): string
    {
        return $this->fieldsSpacesInTcaColumnsOverrides . self::FIELDS_TAB;
    }

    /**
     * @param string|null $fieldsSpacesInTcaColumnsOverrides
     */
    public function setFieldsSpacesInTcaColumnsOverrides(? string $fieldsSpacesInTcaColumnsOverrides): void
    {
        $this->fieldsSpacesInTcaColumnsOverrides = $fieldsSpacesInTcaColumnsOverrides;
    }

    /**
     * @return string
     */
    public function getFieldsSpacesInTcaColumn(): string
    {
        return $this->fieldsSpacesInTcaColumn;
    }

    /**
     * @return string
     */
    public function getFieldsSpacesInTcaColumnConfig(): string
    {
        return $this->fieldsSpacesInTcaColumn . self::FIELDS_TAB;
    }

    /**
     * @return string
     */
    public function getFieldsSpacesInTcaColumnConfigItems(): string
    {
        return $this->getFieldsSpacesInTcaColumnConfig() . self::FIELDS_TAB;
    }

    /**
     * @param string|null $fieldsSpacesInTcaColumn
     */
    public function setFieldsSpacesInTcaColumn(? string $fieldsSpacesInTcaColumn): void
    {
        $this->fieldsSpacesInTcaColumn = $fieldsSpacesInTcaColumn;
    }

    /**
     * @return ObjectStorage|null
     */
    public function getFields(): ? ObjectStorage
    {
        return $this->fields;
    }

    /**
     * @param ObjectStorage|null $fields
     */
    public function setFields(? ObjectStorage $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @return bool
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function areAllFieldsDefault(): bool
    {
        return FieldsCreateCommandUtility::areAllFieldsDefault($this->fields, $this->table);
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
    public function getTranslationPath(): string
    {
        return 'public/typo3conf/ext/' . $this->getExtensionName() . '/Resources/Private/Language/locallang_db.xlf';
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
     * @return string
     */
    public function getMainExtensionInNameSpaceFormat(): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $this->getMainExtension())));
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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
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
    public function getModelDirPath()
    {
        return $this->modelDirPath;
    }

    /**
     * @return string
     */
    public function getNamesFromModelPath(): string
    {
        $tcaRelativePath = explode('/', $this->getModelDirPath());

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
    public function setModelNamespace(string $modelNamespace = '')
    {
        if ($modelNamespace) {
            $this->modelNamespace = $modelNamespace;
        } else {
            $this->modelNamespace = $this->getType() === ElementSetup::CONTENT_ELEMENT ?
                $this->getVendor() . '\\' . $this->getExtensionNameSpaceFormat() . '\Domain\Model\ContentElement':
                $this->getVendor() . '\\' . $this->getExtensionNameSpaceFormat() . '\Domain\Model';
        }
    }

    /**
     * @param string $modelDirPath
     */
    public function setModelDirPath(string $modelDirPath = '')
    {
        if ($modelDirPath) {
            $this->modelDirPath = $modelDirPath;
        } else {
            $this->modelDirPath = $this->getType() === ElementSetup::CONTENT_ELEMENT ?
                'public/typo3conf/ext/' . $this->getExtensionName() . '/Classes/Domain/Model/ContentElement':
                'public/typo3conf/ext/' . $this->getExtensionName() . '/Classes/Domain/Model';
        }
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
    public function getCreateCommandCustomData()
    {
        $mainExtension = $this->getMainExtensionInNameSpaceFormat();
        $vendor = $this->getVendor();

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
        if ($this->getExtensionName() === $this->getMainExtension()) {
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
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function getRegisterPluginFlexFormClass(): string
    {
        $overridePluginFlexFormClass = $this->getCreateCommandOverrideClasses()['registerPluginFlexForm'];
        return $overridePluginFlexFormClass ? $overridePluginFlexFormClass : $this->registerPluginFlexFormClass;
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
        $pathToTypoScriptConstants = $this->getCreateCommandOverrideClasses()['typoScriptConstantsPath'];

        if (empty($pathToTypoScriptConstants)) {
            throw new InvalidArgumentException(
                'Path to TypoScript constants can not be empty. Please fill in it in CreateCommandCustomData.php'
            );
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
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $this->getExtensionName())));
    }

    /**
     * @return string
     */
    public function getTypoScriptPath()
    {
        return 'public/typo3conf/ext/' . $this->getExtensionName() . '/ext_typoscript_setup.typoscript';
    }

    /**
     * @return string
     */
    public function getFlexFormPath()
    {
        return $this->getType() === ElementSetup::CONTENT_ELEMENT ?
            "public/typo3conf/ext/" . $this->getExtensionName() . "/Configuration/FlexForms/ContentElement/" .
            strtolower($this->getExtensionNameSpaceFormat()) . "_" . strtolower($this->getName()) . '.xml' :
            'public/typo3conf/ext/' . $this->getExtensionName() . "/Configuration/FlexForms/"  . $this->getName() . '.xml';
    }

    /**
     * @return string
     */
    public function getFlexFormDirPath()
    {
        $flexFormPath = explode('/', $this->getFlexFormPath());
        array_pop($flexFormPath);

        return implode('/', $flexFormPath);
    }

    /**
     * @return string
     */
    public function getControllerPath()
    {
        return 'public/typo3conf/ext/' . $this->getExtensionName() . '/Classes/Controller/' .
            $this->getControllerName() . 'Controller.php';
    }

    /**
     * @return string
     */
    public function getControllerDirPath()
    {
        $controllerPath = explode('/', $this->getControllerPath());
        array_pop($controllerPath);

        return implode('/', $controllerPath);
    }

    /**
     * @return string
     */
    public function getContentElementClassPath()
    {
        return 'public/typo3conf/ext/' . $this->getExtensionName() . '/Classes/ContentElement/' .
            $this->getName() . '.php';
    }

    /**
     * @return string
     */
    public function getContentElementClassDirPath()
    {
        $contentElementPath = explode('/', $this->getContentElementClassPath());
        array_pop($contentElementPath);

        return implode('/', $contentElementPath);
    }

    /**
     * @return string
     */
    public function getExtTablesPhpPath()
    {
        return 'public/typo3conf/ext/' . $this->getExtensionName() . '/ext_tables.php';
    }

    /**
     * @return string
     */
    public function getExtTablesSqlPath()
    {
        return 'public/typo3conf/ext/' . $this->getExtensionName() . '/ext_tables.sql';
    }

    /**
     * @return string
     */
    public function getTtContentPath()
    {
        return 'public/typo3conf/ext/' . $this->getExtensionName() . '/Configuration/TCA/Overrides/tt_content.php';
    }

    /**
     * @return string
     */
    public function getExtTypoScriptSetupPath()
    {
        return 'public/typo3conf/ext/' . $this->getExtensionName() . '/ext_typoscript_setup.typoscript';
    }

    /**
     * @return string
     */
    public function getExtLocalConfPath()
    {
        return 'public/typo3conf/ext/' . $this->getExtensionName() . '/ext_localconf.php';
    }

    /**
     * @return string
     */
    public function getDefaultIconPath()
    {
        return 'public/typo3conf/ext/content_element_registry/Resources/Public/Icons/CEDefaultIcon.svg';
    }

    /**
     * @return mixed
     */
    public function getIconPath()
    {
        switch ($this->getType()) {
            case ElementSetup::CONTENT_ELEMENT:
                return 'public/typo3conf/ext/' . $this->getExtensionName() . '/Resources/Public/Icons/ContentElement/' .
                    strtolower($this->getExtensionNameSpaceFormat()) . '_' . strtolower($this->getName()) . '.svg';
                break;
            case ElementSetup::PAGE_TYPE:
                return [
                    'inMenu' => 'public/typo3conf/ext/' . $this->getExtensionName() . '/Resources/Public/Icons/dw-page-type-' .
                        $this->getDoktype() . '.svg',
                    'notInMenu' => 'public/typo3conf/ext/' . $this->getExtensionName() . '/Resources/Public/Icons/dw-page-type-' .
                        $this->getDoktype() . '-not-in-menu.svg',
                ];
                break;
            case ElementSetup::INLINE:
                return 'public/typo3conf/ext/' . $this->getExtensionName() . '/Resources/Public/Icons/' .
                    str_replace(' ', '', $this->getStaticType()) . '/' .
                    strtolower($this->getExtensionNameSpaceFormat()) . '_' .
                    strtolower($this->getNamesFromModelPath()) . '.svg';
                break;
            default:
                return 'public/typo3conf/ext/' . $this->getExtensionName() . '/Resources/Public/Icons/' . $this->getName() . '.svg';
        }
    }

    /**
     * @return string
     */
    public function getIconDirPath()
    {
        $iconPath = explode('/', $this->getIconPath());
        array_pop($iconPath);

        return implode('/', $iconPath);
    }

    /**
     * @return string
     */
    public function getDefaultPreviewPath()
    {
        return 'public/typo3conf/ext/content_element_registry/Resources/Public/Images/NewContentElement1.png';
    }

    /**
     * @return string
     */
    public function getDefaultPageTemplatePath()
    {
        return 'public/typo3conf/ext/' . $this->getMainExtension() . '/Resources/Private/Templates/Page/Default.html';
    }

    /**
     * @return string
     */
    public function getModTSConfigPath()
    {
        return 'public/typo3conf/ext/' . $this->getMainExtension() . '/Configuration/TSconfig/Page/Includes/Mod.tsconfig';
    }

    /**
     * @return string
     */
    public function getConfigurationOverridesDirPath()
    {
        return 'public/typo3conf/ext/' . $this->getExtensionName() . '/Configuration/TCA/Overrides';
    }

    /**
     * @return string
     */
    public function getTCADirPath()
    {
        return 'public/typo3conf/ext/' . $this->getExtensionName() . '/Configuration/TCA';
    }

    /**
     * @return string
     */
    public function getTypoScriptMainExtensionConfigPath()
    {
        return 'public/typo3conf/ext/' . $this->getMainExtension() . '/Configuration/TypoScript/Extensions/' .
            str_replace(' ', '', ucwords(str_replace('_', ' ', $this->getMainExtension()))) .
            '.typoscript';
    }

    /**
     * @return string|string[]
     */
    public function getPreviewPath()
    {
        switch ($this->getType()) {
            case ElementSetup::PLUGIN:
                return 'public/typo3conf/ext/' . $this->getMainExtension() .
                    '/Resources/Public/Images/ContentElementPreviews/plugins_' .
                    strtolower($this->getName()) . '.png';
                break;
            default:
                return 'public/typo3conf/ext/' . $this->getMainExtension() .
                    '/Resources/Public/Images/ContentElementPreviews/common_' .
                    strtolower($this->getExtensionNameSpaceFormat()) . '_' .
                    strtolower($this->getName()) . '.png';
        }
    }

    /**
     * @return string|null
     */
    public function getTemplatePath()
    {
        switch ($this->getType()) {
            case ElementSetup::CONTENT_ELEMENT:
                return 'public/typo3conf/ext/' . $this->getExtensionName() .
                    '/Resources/Private/Templates/ContentElements/' . $this->getName() . '.html';
                break;
            case ElementSetup::PAGE_TYPE:
                return 'public/typo3conf/ext/' . $this->getMainExtension() . '/Resources/Private/Partials/PageType/' .
                    $this->getName() . '/Header.html';
                break;
            case ElementSetup::PLUGIN:
                return 'public/typo3conf/ext/' . $this->getExtensionName() .
                    '/Resources/Private/Templates/' . $this->getControllerName() . '/' .
                    ucfirst($this->getActionName()) . '.html';
                break;
        }

        return null;
    }

    /**
     * @return string
     */
    public function getTemplateDirPath()
    {
        $templatePath = explode('/', $this->getTemplatePath());
        array_pop($templatePath);

        return implode('/', $templatePath);
    }

    /**
     * @param bool $isOverride
     * @return string|null
     */
    public function getTCAPath($isOverride = false)
    {
        if ($isOverride) {
            return 'public/typo3conf/ext/' . $this->getExtensionName() . '/Configuration/TCA/Overrides/' .
                $this->getTable() . '_' . $this->getNamesFromModelPath() . '.php';
        } else {
            return 'public/typo3conf/ext/' . $this->getExtensionName() .
                '/Configuration/TCA/tx_' . strtolower($this->getExtensionNameSpaceFormat()) . '_domain_model_' .
                $this->getNamesFromModelPath() . '.php';
        }
    }

    /**
     * @return string
     */
    public function getTranslationDirPath()
    {
        $translationPath = explode('/', $this->getTranslationPath());
        array_pop($translationPath);

        return implode('/', $translationPath);
    }

    /**
     * @return string
     */
    public function getPreviewDirPath()
    {
        $previewPath = explode('/', $this->getPreviewPath());
        array_pop($previewPath);

        return implode('/', $previewPath);
    }

    /**
     * @return string
     */
    public function getStaticType(): string
    {
        return $this->staticType;
    }

    /**
     * @param string $staticType
     */
    public function setStaticType(string $staticType): void
    {
        $this->staticType = $staticType;
    }
}
