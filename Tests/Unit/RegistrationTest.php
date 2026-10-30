<?php

declare(strict_types=1);

namespace Plan2net\RequireAltText\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Plan2net\RequireAltText\Backend\FormDataProvider\RequireAlternativeText;
use TYPO3\CMS\Backend\Form\FormDataProvider\TcaInputPlaceholders;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class RegistrationTest extends UnitTestCase
{
    protected bool $backupGlobals = true;

    #[Test]
    public function runsOnlyOnceThePlaceholderHoldsTheTextOfTheFile(): void
    {
        $GLOBALS['TYPO3_CONF_VARS'] = [];
        require __DIR__ . '/../../ext_localconf.php';

        self::assertContains(
            TcaInputPlaceholders::class,
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][RequireAlternativeText::class]['depends']
        );
    }

    #[Test]
    public function describesTheFieldInTypo3Version13And14(): void
    {
        $GLOBALS['TYPO3_CONF_VARS'] = [];
        require __DIR__ . '/../../ext_localconf.php';
        $core = 'EXT:core/Resources/Private/Language/locallang_tca.xlf';
        $own = 'EXT:require_alt_text/Resources/Private/Language/core_locallang.xlf';

        self::assertContains($own, $GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride'][$core]);
        self::assertContains($own, $GLOBALS['TYPO3_CONF_VARS']['LANG']['resourceOverrides'][$core]);
    }
}
