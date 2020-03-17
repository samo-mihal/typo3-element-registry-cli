<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;
use Digitalwerk\Typo3ElementRegistryCli\Utility\GeneralCreateCommandUtility;

/**
 * Class ControllerRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render
 */
class ControllerRender
{
    /**
     * @var null
     */
    protected $render = null;

    /**
     * Model constructor.
     * @param RenderCreateCommand $render
     */
    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
    }

    /**
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function template()
    {
        $extensionName = $this->render->getExtensionName();
        $controllerName = $this->render->getControllerName();
        $actionName = $this->render->getActionName();

        if (!file_exists('public/typo3conf/ext/' . $extensionName . '/Classes/Controller/' . $controllerName . 'Controller.php')) {
            mkdir('public/typo3conf/ext/' . $extensionName . '/Resources/Private/Templates/' . $controllerName, 0777, true);
            file_put_contents(
                'public/typo3conf/ext/' . $extensionName . '/Classes/Controller/' . $controllerName  . 'Controller.php',
                '<?php
declare(strict_types=1);
namespace Digitalwerk\\' . str_replace(' ','',ucwords(str_replace('_',' ',$extensionName))) . '\Controller;

use ' . $this->render->getPluginControllerExtendClass() . ';

/**
 * Class ' . $controllerName . 'Controller
 * @package Digitalwerk\\' . str_replace(' ','',ucwords(str_replace('_',' ',$extensionName))) . '\Controller
 */
class ' . $controllerName . 'Controller extends ' . end(explode('\\', $this->render->getPluginControllerExtendClass())) . '
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
                'public/typo3conf/ext/' . $extensionName . '/Classes/Controller/' . $controllerName . 'Controller.php',
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
