<?php
namespace Digitalwerk\Typo3ElementRegistryCli\ElementObjects;

use Digitalwerk\Typo3ElementRegistryCli\Utility\Validators;
use Symfony\Component\Console\Question\Question;

/**
 * Class RecordObject
 * @package Digitalwerk\Typo3ElementRegistryCli\ElementObjects
 */
class RecordObject extends AbstractElementObject
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
     * @return void
     */
    public function questions(): void
    {
        $this->name = $this->questionHelper->ask(
            $this->input,
            $this->output,
            (new Question('Record name: '))
                ->setValidator(function ($value) {
                    Validators::notEmpty($value);
                    Validators::camelCase($value);

            // TODO: unique record
                    return $value;
                })
        );

        $this->title = $this->questionHelper->ask(
            $this->input,
            $this->output,
            (new Question('Record title: '))
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
}