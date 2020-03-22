<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Typo3 element registry cli',
    'description' => 'Helper for creating typo3 content elements, page types or plugins with cli commands.',
    'category' => 'be',
    'author' => 'Samuel Mihal',
    'author_email' => 'samuel.mihal@digitalwerk.agency',
    'author_company' => 'Digitalwerk',
    'state' => 'stable',
    'version' => '0.0.8',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-9.5.999',
            'php' => '7.0.0-7.3.999',
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Digitalwerk\\Typo3ElementRegistryCli\\' => 'Classes'
        ]
    ],
];
