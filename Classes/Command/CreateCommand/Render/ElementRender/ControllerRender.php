<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\PHPClassBuilder\Object\PHPClassObject;
use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

/**
 * Class ControllerRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
class ControllerRender extends AbstractRender
{
    /**
     * @var PHPClassObject
     */
    protected $controllerClass = null;

    /**
     * ControllerRender constructor.
     * @param ElementRender $elementRender
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function __construct(ElementRender $elementRender)
    {
        parent::__construct($elementRender);
        $this->controllerClass = new PHPClassObject($this->element->getControllerPath());
        $this->controllerClass->setName($this->element->getControllerName() . 'Controller');
        $this->controllerClass->setStrictMode(true);
        $this->controllerClass->setNameSpace($this->element->getControllerNameSpace());
        $this->controllerClass->setExtendsOrImplements(
            'extends ' . $this->element->getPluginControllerExtendClass()
        );
        $this->controllerClass->setComment(
            '/**
 * Class ' . $this->element->getControllerName() . '
 * @package ' . $this->element->getControllerNameSpace() . '
 */'
        );
    }

    /**
     * ControllerRender destructor.
     */
    public function __destruct()
    {
        $this->controllerClass->render();
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function template()
    {
        if ($this->element->getActionName()) {
            if ($this->controllerClass->contains()->function($this->element->getActionName() . 'action') === false) {
                $this->controllerClass->addFunction()
                    ->setName($this->element->getActionName() . 'Action')
                    ->setType('public function')
                    ->setComment(
                        '/**
     * ' . ucfirst($this->element->getActionName()) . ' action
     */'
                    )
                    ->setArgumentsAndDescription('()')
                    ->setContent(
                        '{' . "\n" . "\n" .
                        $this->controllerClass->getTabSpaces() . '}'
                    );
            }
        }
    }
}
