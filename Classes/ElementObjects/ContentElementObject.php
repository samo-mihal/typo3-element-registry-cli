<?php
namespace Digitalwerk\Typo3ElementRegistryCli\ElementObjects;

use Digitalwerk\Typo3ElementRegistryCli\Validation\Validators;
use Symfony\Component\Console\Question\Question;
use function Symfony\Component\String\u;

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

                    $cType = u($this->makeCommand->extension)->camel()->lower() . '_' . u($value)->lower();
                    Validators::unique(
                        $cType,
                        array_keys($GLOBALS['TCA'][$this->makeCommand->table]['types']),
                        'Content element already exists.'
                    );

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
