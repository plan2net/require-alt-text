<?php

declare(strict_types=1);

namespace Plan2net\RequireAltText\Backend\FormDataProvider;

use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RequireAlternativeText implements FormDataProviderInterface
{
    /**
     * Add form data to result array.
     *
     * @param array<string, mixed> $result Initialized result array
     *
     * @return array<string, mixed> Result filled with more data
     *
     * @psalm-suppress UnusedVariable
     */
    public function addData(array $result): array
    {
        if (!$this->isImageRelationWithAlternativeText($result)) {
            return $result;
        }

        $configuration = $this->getAlternativeTextFieldConfiguration($result);
        // If a placeholder is present it is used by default if the submitted value is empty,
        // otherwise all related options are removed and the editor is forced to supply a text
        if ($this->placeholderIsEmpty($configuration)) {
            $configuration = $this->removeDefaultAndNullableOptions($configuration);
            $configuration = $this->setRequiredFieldEvaluation($configuration);
            $configuration = $this->removePlaceholderOption($configuration);

            $result = $this->setAlternativeTextFieldConfiguration($configuration, $result);
        }

        return $result;
    }

    private function isImageRelationWithAlternativeText(array $configuration): bool
    {
        return $configuration['tableName'] === 'sys_file_reference'
            && isset($configuration['processedTca']['columns']['alternative']['config']);
    }

    private function placeholderIsEmpty(mixed $configuration): bool
    {
        return !isset($configuration['placeholder']) || trim($configuration['placeholder']) === '';
    }

    /**
     * @param array<array-key, mixed> $configuration
     *
     * @return array<array-key, mixed>
     */
    private function removeDefaultAndNullableOptions(array $configuration): array
    {
        // Disable NULL values (hides the "Set value" checkbox above input field)
        if (array_key_exists('default', $configuration) && $configuration['default'] === null) {
            unset($configuration['default']);
        }

        if (isset($configuration['nullable']) && $configuration['nullable']) {
            $configuration['nullable'] = false;
        }

        return $configuration;
    }

    /**
     * @param array<array-key, mixed> $configuration
     *
     * @return array<array-key, mixed>
     */
    private function getAlternativeTextFieldConfiguration(array $configuration): array
    {
        return $configuration['processedTca']['columns']['alternative']['config'];
    }

    /**
     * @param array<array-key, mixed> $configuration
     *
     * @return array<array-key, mixed>
     */
    private function setRequiredFieldEvaluation(array $configuration): array
    {
        $evalCodes = GeneralUtility::trimExplode(',', $configuration['eval'] ?? '', true);
        $evalCodes = array_filter($evalCodes, static function ($value): bool {
            return $value !== 'null' && $value !== 'required';
        });
        $evalCodes[] = 'required';
        $configuration['eval'] = implode(',', $evalCodes);

        return $configuration;
    }

    /**
     * @param array<array-key, mixed> $configuration
     *
     * @return array<array-key, mixed>
     */
    private function removePlaceholderOption(array $configuration): array
    {
        unset($configuration['placeholder']);
        if (isset($configuration['mode']) && $configuration['mode'] === 'useOrOverridePlaceholder') {
            unset($configuration['mode']);
        }

        return $configuration;
    }

    /**
     * @param array<array-key, mixed> $configuration
     * @param array<array-key, mixed> $result
     *
     * @return array<array-key, mixed>
     */
    private function setAlternativeTextFieldConfiguration(array $configuration, array $result): array
    {
        $result['processedTca']['columns']['alternative']['config'] = $configuration;

        return $result;
    }
}
