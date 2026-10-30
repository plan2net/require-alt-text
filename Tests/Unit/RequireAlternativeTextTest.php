<?php

declare(strict_types=1);

namespace Plan2net\RequireAltText\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Plan2net\RequireAltText\Backend\FormDataProvider\RequireAlternativeText;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class RequireAlternativeTextTest extends UnitTestCase
{
    private RequireAlternativeText $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new RequireAlternativeText();
    }

    #[Test]
    public function addDataReturnsUnmodifiedArrayForNonImageReference(): void
    {
        $input = ['tableName' => 'some_other_table'];
        $result = $this->subject->addData($input);
        self::assertSame($input, $result);
    }

    #[Test]
    public function addDataReturnsUnmodifiedArrayWhenPlaceholderIsPresent(): void
    {
        $input = [
            'tableName' => 'sys_file_reference',
            'processedTca' => [
                'columns' => [
                    'alternative' => [
                        'config' => [
                            'placeholder' => 'some placeholder'
                        ]
                    ]
                ]
            ]
        ];
        $result = $this->subject->addData($input);
        self::assertSame($input, $result);
    }

    #[Test]
    public function addDataModifiesConfigurationWhenPlaceholderIsEmpty(): void
    {
        $input = [
            'tableName' => 'sys_file_reference',
            'processedTca' => [
                'columns' => [
                    'alternative' => [
                        'config' => [
                            'placeholder' => '',
                            'default' => null,
                            'nullable' => true,
                            'eval' => 'trim,null',
                            'mode' => 'useOrOverridePlaceholder'
                        ]
                    ]
                ]
            ]
        ];

        $expected = [
            'tableName' => 'sys_file_reference',
            'processedTca' => [
                'columns' => [
                    'alternative' => [
                        'config' => [
                            'nullable' => false,
                            'eval' => 'trim',
                            'required' => true
                        ]
                    ]
                ]
            ]
        ];

        $result = $this->subject->addData($input);
        self::assertEquals($expected, $result);
    }

    #[Test]
    public function addDataModifiesConfigurationWhenPlaceholderIsMissing(): void
    {
        $input = [
            'tableName' => 'sys_file_reference',
            'processedTca' => [
                'columns' => [
                    'alternative' => [
                        'config' => [
                            'default' => null,
                            'nullable' => true,
                            'eval' => 'trim,null'
                        ]
                    ]
                ]
            ]
        ];

        $expected = [
            'tableName' => 'sys_file_reference',
            'processedTca' => [
                'columns' => [
                    'alternative' => [
                        'config' => [
                            'nullable' => false,
                            'eval' => 'trim',
                            'required' => true
                        ]
                    ]
                ]
            ]
        ];

        $result = $this->subject->addData($input);
        self::assertEquals($expected, $result);
    }

    #[Test]
    #[DataProvider('evaluationStringDataProvider')]
    public function addDataHandlesVariousEvaluationStrings(string $inputEval, ?string $expectedEval): void
    {
        $input = [
            'tableName' => 'sys_file_reference',
            'processedTca' => [
                'columns' => [
                    'alternative' => [
                        'config' => [
                            'eval' => $inputEval
                        ]
                    ]
                ]
            ]
        ];

        $result = $this->subject->addData($input);
        self::assertSame($expectedEval, $result['processedTca']['columns']['alternative']['config']['eval'] ?? null);
        self::assertTrue($result['processedTca']['columns']['alternative']['config']['required']);
    }

    /**
     * @return array<string, array{string, ?string}>
     */
    public static function evaluationStringDataProvider(): array
    {
        return [
            'empty eval' => ['', null],
            'single eval' => ['trim', 'trim'],
            'multiple evals' => ['trim,null', 'trim'],
            'required in eval, as before TYPO3 12' => ['trim,required', 'trim'],
            'complex eval string' => ['trim,null,alphanum,required', 'trim,alphanum'],
        ];
    }

    #[Test]
    public function addDataHandlesNonExistentEvalKey(): void
    {
        $input = [
            'tableName' => 'sys_file_reference',
            'processedTca' => [
                'columns' => [
                    'alternative' => [
                        'config' => [
                            // no 'eval' key
                        ]
                    ]
                ]
            ]
        ];

        $result = $this->subject->addData($input);
        self::assertArrayNotHasKey('eval', $result['processedTca']['columns']['alternative']['config']);
        self::assertTrue($result['processedTca']['columns']['alternative']['config']['required']);
    }

    #[Test]
    public function addDataPreservesOtherConfigurationKeys(): void
    {
        $input = [
            'tableName' => 'sys_file_reference',
            'processedTca' => [
                'columns' => [
                    'alternative' => [
                        'config' => [
                            'type' => 'input',
                            'size' => 30,
                            'eval' => 'trim'
                        ]
                    ]
                ]
            ]
        ];

        $result = $this->subject->addData($input);
        self::assertEquals('input', $result['processedTca']['columns']['alternative']['config']['type']);
        self::assertEquals(30, $result['processedTca']['columns']['alternative']['config']['size']);
    }

    #[Test]
    public function addDataHandlesEmptyConfiguration(): void
    {
        $input = [
            'tableName' => 'sys_file_reference',
            'processedTca' => [
                'columns' => [
                    'alternative' => [
                        'config' => []
                    ]
                ]
            ]
        ];

        $result = $this->subject->addData($input);
        self::assertIsArray($result['processedTca']['columns']['alternative']['config']);
        self::assertTrue($result['processedTca']['columns']['alternative']['config']['required']);
    }
}
