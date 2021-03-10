<?php
namespace Digitalwerk\Typo3ElementRegistryCli\ElementObjects;

use Digitalwerk\Typo3ElementRegistryCli\Utility\Validators;
use Symfony\Component\Console\Question\Question;
use function Symfony\Component\String\u;

/**
 * Class PluginObject
 * @package Digitalwerk\Typo3ElementRegistryCli\ElementObjects
 */
class PluginObject extends AbstractElementObject
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $actionName = '';

    /**
     * @var string
     */
    protected $controllerName = '';

    /**
     * @return void
     */
    public function questions(): void
    {
        $this->name = $this->questionHelper->ask(
            $this->input,
            $this->output,
            (new Question('Plugin name: '))
                ->setValidator(function ($value) {
                    Validators::notEmpty($value);
                    Validators::camelCase($value);
                    $extensionCamelCase = u($this->makeCommand->extension)->camel()->title(true)->toString();

                    Validators::unique(
                        $value,
                        array_keys(
                            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions']
                            [$extensionCamelCase]['plugins']
                        ),
                        'Plugin already exists.'
                    );

                    return $value;
                })
        );

        $this->title = $this->questionHelper->ask(
            $this->input,
            $this->output,
            (new Question('Plugin title: '))
                ->setValidator(function ($value) {
                    Validators::notEmpty($value);

                    return $value;
                })
        );

        $this->controllerName = $this->questionHelper->ask(
            $this->input,
            $this->output,
            (new Question('Plugin controller name: '))
                ->setValidator(function ($value) {
                    Validators::notEmpty($value);
                    Validators::camelCase($value);

                    return $value;
                })
        );

        $this->actionName = $this->questionHelper->ask(
            $this->input,
            $this->output,
            (new Question('Plugin action name: '))
                ->setValidator(function ($value) {
                    Validators::notEmpty($value);
                    Validators::camelCase($value, false);

                    return $value;
                })
        );
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getActionName(): string
    {
        return $this->actionName;
    }

    /**
     * @return string
     */
    public function getControllerName(): string
    {
        return $this->controllerName;
    }
}
