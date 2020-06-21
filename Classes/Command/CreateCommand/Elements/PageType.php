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
     * @param ElementObject $elementObject
     */
    public function __construct(ElementObject $elementObject)
    {
        parent::__construct($elementObject);
    }

    /**
     * @return int|void
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function createElement()
    {
        $name = $this->elementObject->getName();
        $doktype = $this->elementObject->getDoktype();

        $this->elementObject->setFieldsSpacesInTcaColumnsOverrides('               ');

        $this->elementRender->setElement($this->elementObject);
        $this->elementRender->check()->pageTypeCreateCommand();
        $this->elementRender->icon()->copyPageTypeDefaultIcon();
        $this->elementRender->model()->pageTypeTemplate();
        $this->elementRender->tca()->pageTypeTemplate();
        $this->elementRender->typoScript()->pageTypeTypoScriptConstants();
        $this->elementRender->typoScript()->pageTypeTypoScriptSubclassOfDefaultPage();
        $this->elementRender->typoScript()->pageTypeTypoScriptRegister();
        $this->elementRender->template()->pageTypeTemplate();
        $this->elementRender->translation()->addFieldsTitleToTranslation();
        $this->elementRender->translation()->addStringToTranslation(
            'page.type.'. $doktype . '.label',
            $this->elementObject->getTitle()
        );
        $this->elementRender->register()->pageTypeToExtTables();
        $this->elementRender->sqlDatabase()->defaultFields();
        $this->elementRender->inline()->render();
        $this->elementRender->typo3Cms()->compareDatabase();
        $this->elementRender->typo3Cms()->clearCache();
        $this->elementObject->getOutput()
            ->writeln('<bg=green;options=bold>Page type ' . $name . ' was created.</>');
    }
}
