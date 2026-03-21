<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Require alternative text',
    'description' => 'Require alternative text for images in TYPO3 CMS',
    'category' => 'be',
    'author' => 'Stefan Hekele, Ioulia Kondratovitch, Wolfgang Klinger',
    'author_email' => 'office@plan2.net',
    'author_company' => 'plan2net GmbH',
    'state' => 'stable',
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-14.99.99',
            'php' => '8.2.0-8.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
