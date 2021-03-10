<?php

return [
    'dw:make:contentElement' => [
        'class' => \Digitalwerk\Typo3ElementRegistryCli\Command\ContentElementMakeCommand::class,
        'schedulable' => false,
    ],
    'dw:make:pageType' => [
        'class' => \Digitalwerk\Typo3ElementRegistryCli\Command\PageTypeMakeCommand::class,
        'schedulable' => false,
    ],
    'dw:make:plugin' => [
        'class' => \Digitalwerk\Typo3ElementRegistryCli\Command\PluginMakeCommand::class,
        'schedulable' => false,
    ],
];
