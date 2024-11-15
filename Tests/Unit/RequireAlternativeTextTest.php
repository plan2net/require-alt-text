<?php

namespace Plan2net\RequireAltText\Tests\Unit;

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

    /**
     * @test
     */
    public function addDataReturnsUnmodifiedArrayForNonImageReference(): void
    {
        $input = ['tableName' => 'some_other_table'];
        $result = $this->subject->addData($input);
        self::assertSame($input, $result);
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
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
                            'eval' => 'trim,required'
                        ]
                    ]
                ]
            ]
        ];

        $result = $this->subject->addData($input);
        self::assertEquals($expected, $result);
    }

    /**
     * @test
     */
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
                            'eval' => 'trim,required'
                        ]
                    ]
                ]
            ]
        ];

        $result = $this->subject->addData($input);
        self::assertEquals($expected, $result);
    }

    /**
     * @test
     * @dataProvider evaluationStringDataProvider
     */
    public function addDataHandlesVariousEvaluationStrings(string $inputEval, string $expectedEval): void
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
        self::assertEquals($expectedEval, $result['processedTca']['columns']['alternative']['config']['eval']);
    }

    public static function evaluationStringDataProvider(): array
    {
        return [
            'empty eval' => ['', 'required'],
            'single eval' => ['trim', 'trim,required'],
            'multiple evals' => ['trim,null', 'trim,required'],
            'already required' => ['trim,required', 'trim,required'],
            'complex eval string' => ['trim,null,alphanum,required', 'trim,alphanum,required'],
        ];
    }

    /**
     * @test
     */
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
        self::assertEquals('required', $result['processedTca']['columns']['alternative']['config']['eval']);
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
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
        self::assertEquals('required', $result['processedTca']['columns']['alternative']['config']['eval']);
    }
}
