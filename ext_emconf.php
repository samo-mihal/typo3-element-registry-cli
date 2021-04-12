<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Typo3 element registry cli',
    'description' => 'Helper for creating typo3 content elements, page types, plugins and records with cli commands.',
    'category' => 'be',
    'author' => 'Samuel Mihal',
    'author_email' => 'samuel.mihal@digitalwerk.agency',
    'author_company' => 'Digitalwerk',
    'state' => 'stable',
    'version' => '10.1.6',
    'constraints' => [
        'depends' => [
            'php' => '7.2.5-8.0.999',
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Digitalwerk\\Typo3ElementRegistryCli\\' => 'Classes'
        ]
    ],
];
