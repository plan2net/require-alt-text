<?php

declare(strict_types=1);

namespace Plan2net\RequireAltText\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
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
        $languageService = $this->createStub(LanguageService::class);
        $languageService->method('sL')->willReturn('Alternative text for images must not be empty!');
        $GLOBALS['LANG'] = $languageService;
    }

    #[Test]
    public function keepsAText(): void
    {
        $this->useMessageQueue($this->createStub(FlashMessageQueue::class));
        $set = true;

        self::assertSame('A cat sitting on a mat', $this->subject->evaluateFieldValue('A cat sitting on a mat', '', $set));
        self::assertTrue($set);
    }

    #[Test]
    #[DataProvider('emptyTexts')]
    public function refusesAnEmptyText(string $text): void
    {
        $this->useMessageQueue($this->createStub(FlashMessageQueue::class));
        $set = true;

        self::assertSame('', $this->subject->evaluateFieldValue($text, '', $set));
        self::assertFalse($set);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function emptyTexts(): array
    {
        return [
            'nothing' => [''],
            'spaces' => ['   '],
            'tab' => ["\t"],
            'line break' => ["\n"],
            'mixed' => [" \t\n "],
        ];
    }

    #[Test]
    public function tellsTheEditorWhyItRefusedTheText(): void
    {
        $queue = $this->createMock(FlashMessageQueue::class);
        $queue->expects(self::once())->method('enqueue');
        $this->useMessageQueue($queue);
        $set = true;

        $this->subject->evaluateFieldValue('', '', $set);
    }

    #[Test]
    public function saysNothingAboutAText(): void
    {
        $queue = $this->createMock(FlashMessageQueue::class);
        $queue->expects(self::never())->method('enqueue');
        $this->useMessageQueue($queue);
        $set = true;

        $this->subject->evaluateFieldValue('Valid alt text', '', $set);
    }

    private function useMessageQueue(FlashMessageQueue $queue): void
    {
        $service = $this->createStub(FlashMessageService::class);
        $service->method('getMessageQueueByIdentifier')->willReturn($queue);
        GeneralUtility::setSingletonInstance(FlashMessageService::class, $service);
    }
}
