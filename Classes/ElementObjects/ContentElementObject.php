<?php
namespace Digitalwerk\Typo3ElementRegistryCli\ElementObjects;

use Digitalwerk\Typo3ElementRegistryCli\Utility\Validators;
use Symfony\Component\Console\Question\Question;

/**
 * Class ContentElementObject
 * @package Digitalwerk\Typo3ElementRegistryCli\ElementObjects
 */
class ContentElementObject extends AbstractElementObject
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
    protected $description;

    /**
     * @return void
     */
    public function questions(): void
    {
        $this->name = $this->questionHelper->ask(
            $this->input,
            $this->output,
            (new Question('Content element name: '))
                ->setValidator(function ($value) {
                    Validators::notEmpty($value);
                    Validators::camelCase($value);

                    return $value;
                })
        );

        $this->title = $this->questionHelper->ask(
            $this->input,
            $this->output,
            (new Question('Content element title: '))
                ->setValidator(function ($value) {
                    Validators::notEmpty($value);

                    return $value;
                })
        );

        $this->description = $this->questionHelper->ask(
            $this->input,
            $this->output,
            (new Question('Content element description: '))
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
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
