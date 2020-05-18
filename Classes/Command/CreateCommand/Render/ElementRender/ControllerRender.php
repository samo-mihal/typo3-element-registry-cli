<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;

/**
 * Class ControllerRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
class ControllerRender extends AbstractRender
{
    /**
     * ControllerRender constructor.
     * @param ElementRender $elementRender
     */
    public function __construct(ElementRender $elementRender)
    {
        parent::__construct($elementRender);
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function template()
    {
        $extensionName = $this->elementRender->getElement()->getExtensionName();
        $controllerName = $this->elementRender->getElement()->getControllerName();
        $actionName = $this->elementRender->getElement()->getActionName();

        if (!file_exists($this->element->getControllerPath())) {
            mkdir($this->element->getControllerDirPath(), 0777, true);
            file_put_contents(
                $this->element->getControllerPath(),
                '<?php
declare(strict_types=1);
namespace Digitalwerk\\' . str_replace(' ','',ucwords(str_replace('_',' ',$extensionName))) . '\Controller;

use ' . $this->elementRender->getElement()->getPluginControllerExtendClass() . ';

/**
 * Class ' . $controllerName . 'Controller
 * @package Digitalwerk\\' . str_replace(' ','',ucwords(str_replace('_',' ',$extensionName))) . '\Controller
 */
class ' . $controllerName . 'Controller extends ' . end(explode('\\', $this->elementRender->getElement()->getPluginControllerExtendClass())) . '
{
    /**
     * ' . ucfirst($actionName) . ' action
     */
    public function ' . $actionName . 'Action()
    {

    }
}'
            );
        } else {
            GeneralCreateCommandUtility::importStringInToFileAfterString(
                $this->element->getControllerPath(),
                [
                    "
    /**
    * " . ucfirst($actionName) . " action
    */
    public function " . $actionName . "Action()
    {

    }
                    "
                ],
                "class " . $controllerName . "Controller extends ActionController",
                1

            );
        }
    }
}
