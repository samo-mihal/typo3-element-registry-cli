<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ContentElement
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand
 */
class ContentElementCreateCommand
{
    /**
     * Table for content elements
     */
    const TABLE = 'tt_content';

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

        $elementRender = GeneralUtility::makeInstance(ElementRender::class);
        $elementRender->setElement($elementObject);
        $elementRender->check()->contentElementCreateCommand();
        $elementRender->contentElementClass()->template();
        $elementRender->model()->contentElementTemplate();
        $elementRender->template()->contentElementTemplate();
        $elementRender->tca()->contentElementTemplate();
        $elementRender->icon()->copyContentElementDefaultIcon();
        $elementRender->previewImage()->copyContentElementDefault();
        $elementRender->sqlDatabase()->defaultFields();
        $elementRender->flexForm()->contentElementTemplate();
        $elementRender->translation()->addStringToTranslation(
            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf',
            $table . '.' . str_replace('_', '', $extensionName) . '_'. strtolower($name) . '.title',
            $elementObject->getTitle()
        );
        $elementRender->translation()->addStringToTranslation(
            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf',
            $table .'.' . str_replace('_', '', $extensionName) . '_'. strtolower($name) . '.description',
            $elementObject->getDescription()
        );
        $elementRender->translation()->addFieldsTitleToTranslation(
            'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf'
        );
        $elementRender->inline()->render();

        $elementRender->typo3Cms()->compareDatabase();
        $elementRender->typo3Cms()->fixFileStructure();
        $elementRender->typo3Cms()->clearCache();
        $elementObject->getOutput()->writeln('<bg=green;options=bold>Content element ' . $name . ' was created.</>');
    }
}
