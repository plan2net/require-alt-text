<?php

declare(strict_types=1);

namespace Plan2net\RequireAltText\Tests\Functional\Evaluation;

use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class AlternativeTextNotEmptyFunctionalTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/require_alt_text',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/be_users.csv');
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/sys_file_storage.csv');
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/sys_file.csv');
        $this->setUpBackendUser(1);
        $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageServiceFactory::class)->create('default');
    }

    /**
     * @test
     */
    public function dataHandlerPersistsAlternativeTextWhenProvided(): void
    {
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start(
            [
                'sys_file_reference' => [
                    'NEW1' => [
                        'pid' => 1,
                        'uid_local' => 1,
                        'uid_foreign' => 1,
                        'tablenames' => 'tt_content',
                        'fieldname' => 'image',
                        'alternative' => 'A descriptive alt text',
                    ],
                ],
            ],
            []
        );
        $dataHandler->process_datamap();

        self::assertEmpty($dataHandler->errorLog, 'DataHandler should have no errors');
        $newUid = $dataHandler->substNEWwithIDs['NEW1'] ?? null;
        self::assertNotNull($newUid, 'Record should have been created');

        $row = $this->getConnectionPool()
            ->getConnectionForTable('sys_file_reference')
            ->select(['alternative'], 'sys_file_reference', ['uid' => $newUid])
            ->fetchAssociative();
        self::assertSame('A descriptive alt text', $row['alternative']);
    }

    /**
     * When the evaluation class receives an empty value, it sets $set=false.
     * DataHandler then excludes that field from the write operation,
     * so the column keeps its database default (null) — the empty string
     * is never persisted.
     *
     * Note: The record itself is still created (other fields are valid).
     * The primary prevention layer is the FormDataProvider which marks the
     * field as required in the backend form, blocking submission entirely.
     *
     * @test
     */
    public function emptyAlternativeTextIsNotPersistedByDataHandler(): void
    {
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start(
            [
                'sys_file_reference' => [
                    'NEW1' => [
                        'pid' => 1,
                        'uid_local' => 1,
                        'uid_foreign' => 1,
                        'tablenames' => 'tt_content',
                        'fieldname' => 'image',
                        'alternative' => '',
                    ],
                ],
            ],
            []
        );
        $dataHandler->process_datamap();

        $newUid = $dataHandler->substNEWwithIDs['NEW1'] ?? null;
        self::assertNotNull($newUid, 'Record is created but empty alt text is excluded');

        $row = $this->getConnectionPool()
            ->getConnectionForTable('sys_file_reference')
            ->select(['alternative'], 'sys_file_reference', ['uid' => $newUid])
            ->fetchAssociative();
        self::assertNull($row['alternative'], 'Empty alt text value should not have been persisted');
    }
}
