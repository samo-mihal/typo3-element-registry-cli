<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Interfaces;

/**
 * Interface MakeCommand
 * @package Digitalwerk\Typo3ElementRegistryCli\Interfaces
 */
interface MakeCommand
{
    /**
     * @return void
     */
    public function beforeMake(): void;

    /**
     * @return void
     */
    public function make(): void;

    /**
     * @return void
     */
    public function afterMake(): void;
}
