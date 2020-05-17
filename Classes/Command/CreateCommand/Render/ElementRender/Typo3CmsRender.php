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
     * @param ElementRender $elementRender
     */
    public function __construct(ElementRender $elementRender)
    {
        parent::__construct($elementRender);
    }

    public function clearCache()
    {
        shell_exec('vendor/bin/typo3cms cache:flush');
        $this->output->writeln('<bg=green;options=bold>Flushed all caches</>');
    }

    public function compareDatabase()
    {
        shell_exec('vendor/bin/typo3cms database:updateschema');
        $this->output->writeln('<bg=green;options=bold>Updated database</>');
    }
}
