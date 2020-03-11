<?php
namespace Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config;

/**
 * Class ImportedClasses
 * @package Digitalwerk\Typo3ElementRegistryCli\Command\CreateCommand\Config
 */
class ImportedClassesConfig
{
    /**
     * @return array
     */
    public function getClasses(): array
    {
        return [
            'objectStorage' => 'use TYPO3\CMS\Extbase\Persistence\ObjectStorage;',
            'titleTrait' => 'use Digitalwerk\DwBoilerplate\Traits\ContentElement\TitleTrait;',
            'textTrait' => 'use Digitalwerk\DwBoilerplate\Traits\ContentElement\TextTrait;',
            'linkTrait' => 'use Digitalwerk\DwBoilerplate\Traits\ContentElement\LinkTrait;',
            'imageTrait' => 'use Digitalwerk\DwBoilerplate\Traits\ContentElement\ImageTrait;',
            'mediaTrait' => 'use Digitalwerk\DwBoilerplate\Traits\ContentElement\MediaTrait;',
            'piFlexFormTrait' => 'use Digitalwerk\DwBoilerplate\Traits\ContentElement\FlexFormTrait;',
        ];
    }
}
