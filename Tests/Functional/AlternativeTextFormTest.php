<?php

declare(strict_types=1);

namespace Plan2net\RequireAltText\Tests\Functional;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Backend\Form\FormDataCompiler;
use TYPO3\CMS\Backend\Form\FormDataGroup\TcaDatabaseRecord;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class AlternativeTextFormTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'plan2net/require-alt-text',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/Fixtures/images.csv');
        $this->setUpBackendUser(1);
        $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageServiceFactory::class)->create('default');
    }

    #[Test]
    public function offersTheTextOfTheFileAndAsksForNothing(): void
    {
        $field = $this->alternativeTextField(1);

        self::assertSame('A dog on a meadow', $field['placeholder'] ?? null);
        self::assertTrue($field['nullable'] ?? false);
        self::assertFalse($field['required'] ?? false);
    }

    #[Test]
    public function asksForATextWhereTheFileHasNone(): void
    {
        $field = $this->alternativeTextField(2);

        self::assertArrayNotHasKey('placeholder', $field);
        self::assertFalse($field['nullable'] ?? false);
        self::assertTrue($field['required'] ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    private function alternativeTextField(int $referenceUid): array
    {
        $request = (new ServerRequest('https://example.com/typo3/', 'GET'))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE);
        $request = $request->withAttribute('normalizedParams', NormalizedParams::createFromRequest($request));
        $input = [
            'request' => $request,
            'tableName' => 'sys_file_reference',
            'vanillaUid' => $referenceUid,
            'command' => 'edit',
        ];
        $result = GeneralUtility::makeInstance(FormDataCompiler::class)
            ->compile($input, GeneralUtility::makeInstance(TcaDatabaseRecord::class));

        return $result['processedTca']['columns']['alternative']['config'];
    }
}
