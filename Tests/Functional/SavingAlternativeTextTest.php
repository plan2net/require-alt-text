<?php

declare(strict_types=1);

namespace Plan2net\RequireAltText\Tests\Functional;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class SavingAlternativeTextTest extends FunctionalTestCase
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
    public function savesAText(): void
    {
        self::assertSame('A cat on a mat', $this->save(2, 'A cat on a mat'));
    }

    // The record is still saved without the text: an eval cannot stop the DataHandler, only the required form field can
    #[Test]
    public function doesNotSaveAnEmptyText(): void
    {
        self::assertNull($this->save(2, '  '));
    }

    private function save(int $referenceUid, string $alternativeText): ?string
    {
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start(['sys_file_reference' => [$referenceUid => ['alternative' => $alternativeText]]], []);
        $dataHandler->process_datamap();

        $alternative = $this->getConnectionPool()
            ->getConnectionForTable('sys_file_reference')
            ->select(['alternative'], 'sys_file_reference', ['uid' => $referenceUid])
            ->fetchOne();

        return $alternative === null ? null : (string)$alternative;
    }
}
