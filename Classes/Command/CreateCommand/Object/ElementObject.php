<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Setup\ElementSetup;
use Digitalwerk\Typo3ElementRegistryCli\Utility\FieldsCreateCommandUtility;
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
    protected $extensionName = '';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $modelPath = '';

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
        return str_replace(' ','',ucwords(str_replace('_',' ', $this->getMainExtension())));
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
    public function getModelPath()
    {
        return $this->modelPath;
    }

    /**
     * @return string
     */
    public function getTCANameFromModelPath(): string
    {
        $tcaRelativePath = explode('/', $this->getModelPath());

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
     * @param string $modelPath
     */
    public function setModelPath(string $modelPath = '')
    {
        if ($modelPath) {
            $this->modelPath = $modelPath;
        } else {
            $this->modelPath = $this->getType() === ElementSetup::CONTENT_ELEMENT ?
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
        return str_replace(' ','',ucwords(str_replace('_',' ', $this->getExtensionName())));
    }

    public function getTypoScriptPath()
    {
        return 'public/typo3conf/ext/' . $this->getExtensionName() . '/ext_typoscript_setup.typoscript';
    }
}
