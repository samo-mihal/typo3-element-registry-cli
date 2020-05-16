<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements\PageType;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements\AbstractElement;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;

/**
 * Class Fields
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements\PageType
 */
class Fields extends AbstractElement
{
    /**
     * Fields constructor.
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
        $name = $elementObject->getName();
        $vendor = $elementObject->getVendor();
        $namespaceToContentElementModel = $vendor . '\\' . $elementObject->getExtensionNameSpaceFormat() . '\Domain\Model';
        $relativePathToModel = $elementObject->getExtensionName() . '/Classes/Domain/Model';

        $elementObject->setFieldsSpacesInTcaColumnsOverrides('               ');
        $elementObject->setInlineRelativePath($relativePathToModel);
        $elementObject->setModelNamespace($namespaceToContentElementModel);
        $elementObject->setStaticName($name);

        $this->elementRender->setElement($elementObject);
        $this->elementRender->check()->pageTypeCreateCommand();
        $this->elementRender->model()->pageTypeTemplate();
        $this->elementRender->tca()->pageTypeTemplate();
        $this->elementRender->typoScript()->pageTypeTypoScriptRegister();
        $this->elementRender->translation()->addFieldsTitleToTranslation(
            $elementObject->getTranslationPath()
        );

        $this->elementRender->sqlDatabase()->defaultFields();
        $this->elementRender->inline()->render();
        $this->elementRender->typo3Cms()->compareDatabase();
        $this->elementRender->typo3Cms()->fixFileStructure();
        $this->elementRender->typo3Cms()->clearCache();
        $elementObject->getOutput()
            ->writeln('<bg=green;options=bold>Content element ' . $name . ' was modified.</>');
    }
}
