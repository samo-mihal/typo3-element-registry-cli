<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;

/**
 * Class PageType
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements
 */
class PageType extends AbstractElement
{
    /**
     * Table for Page types
     */
    const TABLE = 'pages';

    /**
     * PageType constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

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

        $this->elementRender->setElement($elementObject);
        $this->elementRender->check()->pageTypeCreateCommand();
        $this->elementRender->icon()->copyPageTypeDefaultIcon();
        $this->elementRender->model()->pageTypeTemplate();
        $this->elementRender->tca()->pageTypeTemplate();
        $this->elementRender->typoScript()->pageTypeTypoScriptConstants();
        $this->elementRender->typoScript()->pageTypeTypoScriptSubclassOfDefaultPage();
        $this->elementRender->typoScript()->pageTypeTypoScriptRegister();
        $this->elementRender->template()->pageTypeTemplate();
        $this->elementRender->translation()->addFieldsTitleToTranslation(
            $elementObject->getTranslationPath()
        );
        $this->elementRender->translation()->addStringToTranslation(
            $elementObject->getTranslationPath(),
            'page.type.'. $doktype . '.label',
            $elementObject->getTitle()
        );
        $this->elementRender->register()->pageTypeToExtTables();
        $this->elementRender->sqlDatabase()->defaultFields();
        $this->elementRender->inline()->render();
        $this->elementRender->typo3Cms()->compareDatabase();
        $this->elementRender->typo3Cms()->fixFileStructure();
        $this->elementRender->typo3Cms()->clearCache();
        $elementObject->getOutput()
            ->writeln('<bg=green;options=bold>Page type ' . $name . ' was created.</>');
    }
}
