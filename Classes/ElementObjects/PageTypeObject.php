<?php
namespace Digitalwerk\Typo3ElementRegistryCli\ElementObjects;

use Digitalwerk\Typo3ElementRegistryCli\Utility\Validators;
use Symfony\Component\Console\Question\Question;

/**
 * Class PageTypeObject
 * @package Digitalwerk\Typo3ElementRegistryCli\ElementObjects
 */
class PageTypeObject extends AbstractElementObject
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
     * @var int
     */
    protected $doktype;

    /**
     * @return void
     */
    public function questions(): void
    {
        $this->doktype = $this->questionHelper->ask(
            $this->input,
            $this->output,
            (new Question('Page type doktype: '))
                ->setValidator(function ($value) {
                    Validators::notEmpty($value);
                    Validators::integer($value);
                    Validators::unique($value, array_keys($GLOBALS['TCA']['pages']['types']));

                    return $value;
                })
        );

        $this->name = $this->questionHelper->ask(
            $this->input,
            $this->output,
            (new Question('Page type name: '))
                ->setValidator(function ($value) {
                    Validators::notEmpty($value);
                    Validators::camelCase($value);

                    return $value;
                })
        );

        $this->title = $this->questionHelper->ask(
            $this->input,
            $this->output,
            (new Question('Page type title: '))
                ->setValidator(function ($value) {
                    Validators::notEmpty($value);

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
     * @return int
     */
    public function getDoktype(): int
    {
        return $this->doktype;
    }
}
