<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Object\Element\FieldObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FlexFormRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
class FlexFormRender extends AbstractRender
{
    /**
     * @var FieldsRender
     */
    protected $fieldsRender = null;

    /**
     * FlexFormRender constructor.
     * @param ElementRender $elementRender
     */
    public function __construct(ElementRender $elementRender)
    {
        parent::__construct($elementRender);
        $this->fieldsRender = GeneralUtility::makeInstance(FieldsRender::class, $elementRender);
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function contentElementTemplate()
    {
        $fields = $this->fields;

        if ($fields) {
            /** @var FieldObject $field */
            foreach ($fields as $field) {
                if ($field->isFlexFormItemsAllowed()) {
                    $this->createFlexForm();
                    $this->fieldsRender->fieldsToFlexForm(
                        $this->elementRender->getElement()->getInlineFields()[$field->getFirstItem()->getType()]
                    );
                }
            }
        }
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function pluginTemplate()
    {
        if ($this->fields) {
            $this->createFlexForm();
            $this->fieldsRender->fieldsToFlexForm($this->fields);
        }
    }

    /**
     * @return void
     */
    private function createFlexForm(): void
    {
        if (!file_exists($this->element->getFlexFormPath()) && $this->fields) {
            mkdir($this->element->getFlexFormDirPath(), 0777, true);
            $view = clone $this->view;
            $view->setTemplatePathAndFilename(
                GeneralUtility::getFileAbsFileName(
                    'EXT:typo3_element_registry_cli/Resources/Private/Templates/FlexForm/DefaultTemplate.html'
                )
            );
            file_put_contents($this->element->getFlexFormPath(), $view->render());
        }
    }
}
