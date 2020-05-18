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
    public function createElement()
    {
        $name = $this->elementObject->getName();
        $table = 'tx_' . str_replace('_', '', $this->elementObject->getExtensionName()) .
            '_domain_model_' . strtolower($name);

        $this->elementObject->setFieldsSpacesInTcaColumn('        ');
        $this->elementObject->setTable($table);
        $this->elementObject->setTcaFieldsPrefix(false);

        $this->elementRender->setElement($this->elementObject);
        $this->elementRender->check()->recordCreateCommand();
        $this->elementRender->model()->recordTemplate();
        $this->elementRender->tca()->recordTemplate();
        $this->elementRender->icon()->copyElementDefaultIcon();
        $this->elementRender->sqlDatabase()->recordFields();
        $this->elementRender->translation()->addFieldsTitleToTranslation();
        $this->elementRender->translation()->addStringToTranslation(
            $table,
            $this->elementObject->getTitle()
        );
        $this->elementRender->inline()->render();
        $this->elementRender->typo3Cms()->compareDatabase();
        $this->elementRender->typo3Cms()->clearCache();
        $this->elementObject->getOutput()
            ->writeln('<bg=green;options=bold>Record ' . $name . ' was created.</>');
    }
}
