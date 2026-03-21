<?php

declare(strict_types=1);

namespace Plan2net\RequireAltText\Tests\Unit;

use Plan2net\RequireAltText\Evaluation\AlternativeTextNotEmpty;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class AlternativeTextNotEmptyTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    private AlternativeTextNotEmpty $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new AlternativeTextNotEmpty();

        $languageServiceMock = $this->createMock(LanguageService::class);
        $languageServiceMock->method('sL')->willReturn('Alt text must not be empty');
        $GLOBALS['LANG'] = $languageServiceMock;
    }

    private function registerFlashMessageServiceMock(?FlashMessageQueue $queueMock = null): void
    {
        $queueMock ??= $this->createMock(FlashMessageQueue::class);
        $flashMessageServiceMock = $this->createMock(FlashMessageService::class);
        $flashMessageServiceMock->method('getMessageQueueByIdentifier')->willReturn($queueMock);
        GeneralUtility::setSingletonInstance(FlashMessageService::class, $flashMessageServiceMock);
    }

    /**
     * @test
     */
    public function nonEmptyValueIsReturnedUnchanged(): void
    {
        $set = true;
        $result = $this->subject->evaluateFieldValue('A cat sitting on a mat', '', $set);

        self::assertSame('A cat sitting on a mat', $result);
        self::assertTrue($set);
    }

    /**
     * @test
     */
    public function emptyValueReturnsEmptyStringAndSetsFlagToFalse(): void
    {
        $this->registerFlashMessageServiceMock();

        $set = true;
        $result = $this->subject->evaluateFieldValue('', '', $set);

        self::assertSame('', $result);
        self::assertFalse($set);
    }

    /**
     * @test
     *
     * @dataProvider whitespaceValueDataProvider
     */
    public function whitespaceOnlyValueIsTreatedAsEmpty(string $whitespace): void
    {
        $this->registerFlashMessageServiceMock();

        $set = true;
        $result = $this->subject->evaluateFieldValue($whitespace, '', $set);

        self::assertSame('', $result);
        self::assertFalse($set);
    }

    public static function whitespaceValueDataProvider(): array
    {
        return [
            'spaces' => ['   '],
            'tab' => ["\t"],
            'newline' => ["\n"],
            'mixed whitespace' => [" \t\n "],
        ];
    }

    /**
     * @test
     */
    public function flashMessageIsQueuedWhenValueIsEmpty(): void
    {
        $flashMessageQueueMock = $this->createMock(FlashMessageQueue::class);
        $flashMessageQueueMock->expects(self::once())->method('enqueue');
        $this->registerFlashMessageServiceMock($flashMessageQueueMock);

        $set = true;
        $this->subject->evaluateFieldValue('', '', $set);
    }

    /**
     * @test
     */
    public function noFlashMessageIsQueuedWhenValueIsNonEmpty(): void
    {
        $set = true;
        $result = $this->subject->evaluateFieldValue('Valid alt text', '', $set);

        self::assertSame('Valid alt text', $result);
        self::assertTrue($set);
    }
}
