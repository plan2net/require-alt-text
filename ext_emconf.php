<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Require alternative text',
    'description' => 'Require alternative text for images in TYPO3 CMS',
    'category' => 'be',
    'author' => 'Stefan Hekele, Ioulia Kondratovitch, Wolfgang Klinger',
    'author_email' => 'office@plan2.net',
    'author_company' => 'plan2net GmbH',
    'state' => 'stable',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-13.4.99',
            'php' => '8.1.0-8.3.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
