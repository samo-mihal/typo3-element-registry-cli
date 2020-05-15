<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class PageType
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements
 */
class PageType
{
    /**
     * Table for Page types
     */
    const TABLE = 'pages';

    /**
     * @param ElementObject $elementObject
     * @return int|void
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function execute(ElementObject $elementObject)
    {
        $name = $elementObject->getName();
        $vendor = $elementObject->getVendor();
        $doktype = $elementObject->getDoktype();
        $namespaceToContentElementModel = $vendor . '\\' . $elementObject->getExtensionNameSpaceFormat() . '\Domain\Model';
        $relativePathToModel = $elementObject->getExtensionName() . '/Classes/Domain/Model';

        $elementObject->setFieldsSpacesInTcaColumnsOverrides('               ');
        $elementObject->setInlineRelativePath($relativePathToModel);
        $elementObject->setModelNamespace($namespaceToContentElementModel);
        $elementObject->setStaticName($name);
        $elementObject->setBetweenProtectedsAndGetters(
            implode(
                "\n",
                [
                    '    /**',
                    '     * @var int',
                    '     */',
                    '    protected static $doktype = ' . $doktype . ';' . "\n"
                ]
            )
        );

        $elementRender = GeneralUtility::makeInstance(ElementRender::class);
        $elementRender->setElement($elementObject);
        $elementRender->check()->pageTypeCreateCommand();
        $elementRender->icon()->copyPageTypeDefaultIcon();
        $elementRender->model()->pageTypeTemplate();
        $elementRender->tca()->pageTypeTemplate();
        $elementRender->typoScript()->pageTypeTypoScriptRegister();
        $elementRender->template()->pageTypeTemplate();
        $elementRender->translation()->addFieldsTitleToTranslation(
            $elementObject->getTranslationPath()
        );
        $elementRender->translation()->addStringToTranslation(
            $elementObject->getTranslationPath(),
            'page.type.'. $doktype . '.label',
            $elementObject->getTitle()
        );
        $elementRender->register()->pageTypeToExtTables();
        $elementRender->sqlDatabase()->defaultFields();
        $elementRender->inline()->render();
        $elementRender->typo3Cms()->compareDatabase();
        $elementRender->typo3Cms()->fixFileStructure();
        $elementRender->typo3Cms()->clearCache();
        $elementObject->getOutput()
            ->writeln('<bg=green;options=bold>Page type ' . $name . ' was created.</>');
    }
}
