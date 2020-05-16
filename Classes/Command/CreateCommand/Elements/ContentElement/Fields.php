<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements\ContentElement;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements\AbstractElement;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;

/**
 * Class Fields
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements\ContentElement
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
        $this->elementRender->tca()->contentElementTemplate();
        $this->elementRender->sqlDatabase()->defaultFields();
        $this->elementRender->flexForm()->contentElementTemplate();
        $this->elementRender->translation()->addFieldsTitleToTranslation(
            $elementObject->getTranslationPath()
        );
        $this->elementRender->inline()->render();

        $this->elementRender->typo3Cms()->compareDatabase();
        $this->elementRender->typo3Cms()->fixFileStructure();
        $this->elementRender->typo3Cms()->clearCache();
        $elementObject->getOutput()
            ->writeln('<bg=green;options=bold>Content element ' . $name . ' was modified.</>');
    }
}
