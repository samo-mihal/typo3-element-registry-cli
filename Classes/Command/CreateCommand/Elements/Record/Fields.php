<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements\Record;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements\AbstractElement;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\ElementObject;

/**
 * Class Fields
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Elements\Record
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
        $extensionName = $this->elementObject->getExtensionName();
        $name = $this->elementObject->getName();

        $table = 'tx_' . str_replace('_', '', $extensionName) . '_domain_model_' . strtolower($name);

        $this->elementObject->setFieldsSpacesInTcaColumn('        ');
        $this->elementObject->setTable($table);
        $this->elementObject->setTcaFieldsPrefix(false);

        $this->elementRender->setElement($this->elementObject);
        $this->elementRender->check()->recordCreateCommand();
        $this->elementRender->model()->recordTemplate();
        $this->elementRender->tca()->recordTemplate();
        $this->elementRender->sqlDatabase()->recordFields();
        $this->elementRender->translation()->addFieldsTitleToTranslation();
        $this->elementRender->inline()->render();
        $this->elementRender->typo3Cms()->compareDatabase();
        $this->elementRender->typo3Cms()->clearCache();
        $this->elementObject->getOutput()
            ->writeln('<bg=green;options=bold>Record ' . $name . ' was modified.</>');
    }
}
