<?php

declare(strict_types=1);

namespace Plan2net\RequireAltText\Tests\Functional\Backend\FormDataProvider;

use Plan2net\RequireAltText\Backend\FormDataProvider\RequireAlternativeText;
use Plan2net\RequireAltText\Evaluation\AlternativeTextNotEmpty;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class RequireAlternativeTextFunctionalTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/require_alt_text',
    ];

    /**
     * @test
     */
    public function formDataProviderIsRegisteredAndModifiesTcaConfiguration(): void
    {
        $provider = GeneralUtility::makeInstance(RequireAlternativeText::class);

        $input = [
            'tableName' => 'sys_file_reference',
            'processedTca' => [
                'columns' => [
                    'alternative' => [
                        'config' => [
                            'eval' => 'trim,null',
                            'default' => null,
                            'nullable' => true,
                        ],
                    ],
                ],
            ],
        ];

        $result = $provider->addData($input);

        self::assertStringContainsString('required', $result['processedTca']['columns']['alternative']['config']['eval']);
        self::assertFalse($result['processedTca']['columns']['alternative']['config']['nullable']);
        self::assertArrayNotHasKey('default', $result['processedTca']['columns']['alternative']['config']);
    }

    /**
     * @test
     */
    public function evaluationClassIsRegisteredInGlobals(): void
    {
        self::assertArrayHasKey(
            AlternativeTextNotEmpty::class,
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals']
        );
    }
}
