<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render;

use Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\RenderCreateCommand;

/**
 * Class Typo3CmsRender
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Render
 */
class Typo3CmsRender
{
    /**
     * @var RenderCreateCommand
     */
    protected $render = null;

    /**
     * TypoScript constructor.
     * @param RenderCreateCommand $render
     */
    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
    }

    public function clearCache()
    {
        shell_exec('vendor/bin/typo3cms cache:flush');
        $this->render->getOutput()
            ->writeln('<bg=green;options=bold>Flushed all caches</>');
    }

    public function compareDatabase()
    {
        shell_exec('vendor/bin/typo3cms database:updateschema');
        $this->render->getOutput()
            ->writeln('<bg=green;options=bold>Updated database</>');
    }

    public function fixFileStructure()
    {
        shell_exec('vendor/bin/typo3cms install:fixfolderstructure');
        $this->render->getOutput()
            ->writeln('<bg=green;options=bold>Fixed folder structure</>');
    }
}
