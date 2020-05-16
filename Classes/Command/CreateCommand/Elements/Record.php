<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;

/**
 * Class Record
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements
 */
class Record extends AbstractElement
{
    /**
     * Record constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param ElementObject $elementObject
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function execute(ElementObject $elementObject)
    {
        $extensionName = $elementObject->getExtensionName();
        $name = $elementObject->getName();
        $vendor = $elementObject->getVendor();

        $table = 'tx_' . str_replace('_', '', $extensionName) . '_domain_model_' . strtolower($name);
        $relativePathToModel = $extensionName . '/Classes/Domain/Model';
        $extensionNameInNameSpace = str_replace(' ','',ucwords(str_replace('_',' ',$extensionName)));
        $namespaceToModel = $vendor . '\\' . $extensionNameInNameSpace . '\Domain\Model';

        $elementObject->setFieldsSpacesInTcaColumn('        ');
        $elementObject->setTable($table);
        $elementObject->setInlineRelativePath($relativePathToModel);
        $elementObject->setModelNamespace($namespaceToModel);
        $elementObject->setTcaFieldsPrefix(false);
        $elementObject->setStaticName($name);

        $this->elementRender->setElement($elementObject);
        $this->elementRender->check()->recordCreateCommand();
        $this->elementRender->model()->recordTemplate();
        $this->elementRender->tca()->recordTemplate();
        $this->elementRender->icon()->copyRecordDefaultIcon();
        $this->elementRender->sqlDatabase()->recordFields();
        $this->elementRender->translation()->addFieldsTitleToTranslation(
            $elementObject->getTranslationPath()
        );
        $this->elementRender->translation()->addStringToTranslation(
            $elementObject->getTranslationPath(),
            $table,
            $elementObject->getTitle()
        );
        $this->elementRender->inline()->render();
        $this->elementRender->typo3Cms()->compareDatabase();
        $this->elementRender->typo3Cms()->fixFileStructure();
        $this->elementRender->typo3Cms()->clearCache();
        $elementObject->getOutput()
            ->writeln('<bg=green;options=bold>Record ' . $name . ' was created.</>');
    }
}
