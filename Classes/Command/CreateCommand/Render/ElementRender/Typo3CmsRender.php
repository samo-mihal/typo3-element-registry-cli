<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender;

/**
 * Class Typo3CmsRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render\ElementRender
 */
class Typo3CmsRender extends AbstractRender
{
    /**
     * Typo3CmsRender constructor.
     * @param ElementRender $element
     */
    public function __construct(ElementRender $element)
    {
        parent::__construct($element);
    }

    public function clearCache()
    {
        shell_exec('vendor/bin/typo3cms cache:flush');
        $this->element->getOutput()
            ->writeln('<bg=green;options=bold>Flushed all caches</>');
    }

    public function compareDatabase()
    {
        shell_exec('vendor/bin/typo3cms database:updateschema');
        $this->element->getOutput()
            ->writeln('<bg=green;options=bold>Updated database</>');
    }

    public function fixFileStructure()
    {
        shell_exec('vendor/bin/typo3cms install:fixfolderstructure');
        $this->element->getOutput()
            ->writeln('<bg=green;options=bold>Fixed folder structure</>');
    }
}
