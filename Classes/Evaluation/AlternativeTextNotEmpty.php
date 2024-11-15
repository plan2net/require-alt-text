<?php

declare(strict_types=1);

namespace Plan2net\RequireAltText\Evaluation;

use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AlternativeTextNotEmpty
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     *
     * @throws Exception
     */
    public function evaluateFieldValue(string $value, string $isIn, bool &$set): string
    {
        if ($this->fieldHasA($value)) {
            return $value;
        }

        $this->setFlashMessageForMissingAlternativeText();
        $set = false; // Calling code will ignore the return value

        return '';
    }

    /**
     * @throws Exception
     */
    private function setFlashMessageForMissingAlternativeText(): void
    {
        /** @var FlashMessage $message */
        $message = GeneralUtility::makeInstance(
            FlashMessage::class,
            $GLOBALS['LANG']->sL('LLL:EXT:require_alt_text/Resources/Private/Language/locallang.xlf:error.alternativeTextMustNotBeEmpty'),
            '', // header is optional
            ContextualFeedbackSeverity::ERROR,
            true // whether message should be stored in session
        );
        /** @var FlashMessageService $flashMessageService */
        $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
        $flashMessageService->getMessageQueueByIdentifier()->enqueue($message);
    }

    private function fieldHasA(string $value): bool
    {
        return trim($value) !== '';
    }
}
