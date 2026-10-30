<?php

use Plan2net\RequireAltText\Backend\FormDataProvider\RequireAlternativeText;
use Plan2net\RequireAltText\Evaluation\AlternativeTextNotEmpty;
use TYPO3\CMS\Backend\Form\FormDataProvider\TcaInputPlaceholders;

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][AlternativeTextNotEmpty::class] = '';

// Needs the resolved placeholder: the file's alternative text, or none
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][RequireAlternativeText::class] = [
    'depends' => [
        TcaInputPlaceholders::class,
    ],
];

// TYPO3 13 reads the first, TYPO3 14 the second
$GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['EXT:core/Resources/Private/Language/locallang_tca.xlf'][]
    = 'EXT:require_alt_text/Resources/Private/Language/core_locallang.xlf';
$GLOBALS['TYPO3_CONF_VARS']['LANG']['resourceOverrides']['EXT:core/Resources/Private/Language/locallang_tca.xlf'][]
    = 'EXT:require_alt_text/Resources/Private/Language/core_locallang.xlf';
