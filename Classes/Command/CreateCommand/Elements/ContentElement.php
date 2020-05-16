<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;

/**
 * Class ContentElement
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements
 */
class ContentElement extends AbstractElement
{
    /**
     * Table for content elements
     */
    const TABLE = 'tt_content';

    /**
     * ContentElement constructor.
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
        $table = $elementObject->getTable();
        $name = $elementObject->getName();
        $extensionName = $elementObject->getExtensionName();
        $vendor = $elementObject->getVendor();
        $extensionNameInNameSpace = $elementObject->getExtensionNameSpaceFormat();
        $namespaceToContentElementModel = $vendor . '\\' . $extensionNameInNameSpace . '\Domain\Model\ContentElement';
        $relativePathToModel = $extensionName . '/Classes/Domain/Model/ContentElement';
        $relativePathToClass = $vendor . '\\' . $extensionNameInNameSpace . '\ContentElement\\' . $name;

        $elementObject->setInlineRelativePath($relativePathToModel);
        $elementObject->setModelNamespace($namespaceToContentElementModel);
        $elementObject->setStaticName($name);
        $elementObject->setRelativePathToClass($relativePathToClass);

        $this->elementRender->setElement($elementObject);
        $this->elementRender->check()->contentElementCreateCommand();
        $this->elementRender->contentElementClass()->template();
        $this->elementRender->model()->contentElementTemplate();
        $this->elementRender->template()->contentElementTemplate();
        $this->elementRender->tca()->contentElementTemplate();
        $this->elementRender->icon()->copyContentElementDefaultIcon();
        $this->elementRender->previewImage()->copyContentElementDefault();
        $this->elementRender->sqlDatabase()->defaultFields();
        $this->elementRender->flexForm()->contentElementTemplate();
        $this->elementRender->translation()->addStringToTranslation(
            $elementObject->getTranslationPath(),
            $table . '.' . str_replace('_', '', $extensionName) . '_'. strtolower($name) . '.title',
            $elementObject->getTitle()
        );
        $this->elementRender->translation()->addStringToTranslation(
            $elementObject->getTranslationPath(),
            $table .'.' . str_replace('_', '', $extensionName) . '_'. strtolower($name) . '.description',
            $elementObject->getDescription()
        );
        $this->elementRender->translation()->addFieldsTitleToTranslation(
            $elementObject->getTranslationPath()
        );
        $this->elementRender->inline()->render();

        $this->elementRender->typo3Cms()->compareDatabase();
        $this->elementRender->typo3Cms()->fixFileStructure();
        $this->elementRender->typo3Cms()->clearCache();
        $elementObject->getOutput()
            ->writeln('<bg=green;options=bold>Content element ' . $name . ' was created.</>');
    }
}
