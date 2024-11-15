<?php

use Plan2net\RequireAltText\Evaluation\AlternativeTextNotEmpty;

if(!empty($GLOBALS['TCA']['sys_file_reference']['columns']['alternative']['config']['eval'])) {
    $GLOBALS['TCA']['sys_file_reference']['columns']['alternative']['config']['eval'] .= ',' . AlternativeTextNotEmpty::class;
} else {
    $GLOBALS['TCA']['sys_file_reference']['columns']['alternative']['config']['eval'] = AlternativeTextNotEmpty::class;
}
