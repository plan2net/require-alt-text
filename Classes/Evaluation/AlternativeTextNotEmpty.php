<?php

declare(strict_types=1);

namespace Plan2net\RequireAltText\Evaluation;

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AlternativeTextNotEmpty
{
    public function evaluateFieldValue(string $value, string $isIn, bool &$set): string
    {
        if ($this->fieldHasAlternativeText($value)) {
            return $value;
        }

        $this->setFlashMessageForMissingAlternativeText();
        // Calling code will ignore the return value
        $set = false;

        return '';
    }

    private function setFlashMessageForMissingAlternativeText(): void
    {
        $message = GeneralUtility::makeInstance(
            FlashMessage::class,
            $GLOBALS['LANG']->sL('LLL:EXT:require_alt_text/Resources/Private/Language/locallang.xlf:error.alternativeTextMustNotBeEmpty'),
            '',
            ContextualFeedbackSeverity::ERROR,
            true
        );
        $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
        $flashMessageService->getMessageQueueByIdentifier()->enqueue($message);
    }

    private function fieldHasAlternativeText(string $value): bool
    {
        return trim($value) !== '';
    }
}
