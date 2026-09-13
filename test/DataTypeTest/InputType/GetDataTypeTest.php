<?php

declare(strict_types=1);

namespace DataTypeTest\InputType;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\InputType\GetDataType;
use DataType\Messages;
use DataType\ProcessedValues;
use DataTypeTest\BaseTestCase;
use DataTypeTestFixture\InputType\GetDataTypeFixture;
use DataTypeTestFixture\Integration\ReviewScore;
use VarMap\ArrayVarMap;
use function DataType\processInputTypeWithDataStorage;

/**
 * @covers \DataType\InputType\GetDataType
 */
class GetDataTypeTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{array<string, mixed>, int, string}>
     */
    public static function provides_parses_nested_datatype(): \Generator
    {
        yield 'valid review' => [
            [
                'review' => [
                    'score' => 5,
                    'comment' => 'Hello world',
                ],
            ],
            5,
            'Hello world',
        ];
        yield 'boundary score' => [
            [
                'review' => [
                    'score' => 100,
                    'comment' => 'Great',
                ],
            ],
            100,
            'Great',
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('provides_parses_nested_datatype')]
    public function test_parses_nested_datatype(
        array $data,
        int $expectedScore,
        string $expectedComment
    ): void {
        $result = GetDataTypeFixture::createFromVarMap(new ArrayVarMap($data));

        $this->assertSame($expectedScore, $result->review->getScore());
        $this->assertSame($expectedComment, $result->review->getComment());
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, string, string}>
     */
    public static function provides_fails_with_validation_error(): \Generator
    {
        yield 'missing nested object' => [
            [],
            '/review',
            Messages::VALUE_NOT_SET,
        ];
        yield 'missing nested field' => [
            [
                'review' => [
                    'score' => 5,
                ],
            ],
            '/review/comment',
            Messages::VALUE_NOT_SET,
        ];
        yield 'nested field too short' => [
            [
                'review' => [
                    'score' => 5,
                    'comment' => 'Hi',
                ],
            ],
            '/review/comment',
            Messages::STRING_TOO_SHORT,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('provides_fails_with_validation_error')]
    public function test_fails_with_validation_error(
        array $data,
        string $path,
        string $messagePattern
    ): void {
        try {
            GetDataTypeFixture::createFromVarMap(new ArrayVarMap($data));
            $this->fail('Expected ValidationException was not thrown.');
        } catch (\DataType\Exception\ValidationException $ve) {
            $this->assertValidationProblemRegexp($path, $messagePattern, $ve->getValidationProblems());
        }
    }

    public function test_get_input_type_processes_nested_value(): void
    {
        $getDataType = new GetDataType('review', ReviewScore::class);
        $inputType = $getDataType->getInputType();

        $this->assertSame('review', $inputType->getName());

        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueButRoot(
            'review',
            ['score' => 7, 'comment' => 'Nice work']
        );

        $validationProblems = processInputTypeWithDataStorage(
            $inputType,
            $processedValues,
            $dataStorage
        );

        $this->assertCount(0, $validationProblems);
        [$resultValue, $wasFound] = $processedValues->getValueForTargetProperty('review');
        $this->assertTrue($wasFound);
        $this->assertInstanceOf(ReviewScore::class, $resultValue);
        $this->assertSame(7, $resultValue->getScore());
        $this->assertSame('Nice work', $resultValue->getComment());
    }
}
