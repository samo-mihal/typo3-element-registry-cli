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
     * @param ElementObject $elementObject
     */
    public function __construct(ElementObject $elementObject)
    {
        parent::__construct($elementObject);
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function addFields()
    {
        $this->elementObject->setFieldsSpacesInTcaColumnsOverrides('               ');

        $this->elementRender->setElement($this->elementObject);
        $this->elementRender->check()->pageTypeCreateCommand();
        $this->elementRender->model()->pageTypeTemplate();
        $this->elementRender->tca()->pageTypeTemplate();
        $this->elementRender->typoScript()->pageTypeTypoScriptRegister();
        $this->elementRender->translation()->addFieldsTitleToTranslation();
        $this->elementRender->sqlDatabase()->defaultFields();
        $this->elementRender->inline()->render();
        $this->elementRender->typo3Cms()->compareDatabase();
        $this->elementRender->typo3Cms()->clearCache();
        $this->elementObject->getOutput()
            ->writeln('<bg=green;options=bold>Page type ' . $this->elementObject->getName() . ' was modified.</>');
    }
}
