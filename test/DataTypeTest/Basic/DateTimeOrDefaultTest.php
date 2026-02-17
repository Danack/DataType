<?php

declare(strict_types=1);

namespace DataTypeTest\Basic;

use DataType\Basic\DateTimeOrDefault;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

/**
 * @covers \DataType\Basic\DateTimeOrDefault
 */
class DateTimeOrDefaultTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{array<string, mixed>, bool}>  true = expect DateTimeInterface, false = expect null
     */
    public static function provides_works_parses_input_to_expected(): \Generator
    {
        yield 'valid datetime' => [['until' => '2002-10-02T10:00:00-05:00'], true];
        yield 'missing gives null default' => [[], false];
    }

    /**
     * @dataProvider provides_works_parses_input_to_expected
     * @param array<string, mixed> $data
     */
    public function test_works_parses_input_to_expected(array $data, bool $expectDateTime): void
    {
        $result = DateTimeOrDefaultNullFixture::createFromVarMap(new ArrayVarMap($data));
        if ($expectDateTime) {
            $this->assertInstanceOf(\DateTimeInterface::class, $result->value);
        } else {
            $this->assertNull($result->value);
        }
    }

    public function testMissingGivesDefault(): void
    {
        $default = new \DateTimeImmutable('2020-01-01T00:00:00+00:00');
        $inputType = (new DateTimeOrDefault('until', $default))->getInputType();
        $dataStorage = \DataType\DataStorage\TestArrayDataStorage::createMissing('until');
        $processedValues = new \DataType\ProcessedValues();
        $result = $inputType->getExtractRule()->process($processedValues, $dataStorage);
        $this->assertNoProblems($result);
        $this->assertEquals($default, $result->getValue());
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, string, string}>
     */
    public static function provides_fails_with_validation_error(): \Generator
    {
        yield 'invalid datetime' => [['until' => 'not a date'], '/until', Messages::ERROR_INVALID_DATETIME];
    }

    /**
     * @dataProvider provides_fails_with_validation_error
     * @param array<string, mixed> $data
     */
    public function test_fails_with_validation_error(array $data, string $path, string $messagePattern): void
    {
        try {
            DateTimeOrDefaultNullFixture::createFromVarMap(new ArrayVarMap($data));
            $this->fail('Expected ValidationException was not thrown.');
        } catch (\DataType\Exception\ValidationException $ve) {
            $this->assertValidationProblemRegexp($path, $messagePattern, $ve->getValidationProblems());
        }
    }
}

class DateTimeOrDefaultNullFixture implements DataType
{
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[DateTimeOrDefault('until', null)]
        public readonly \DateTimeInterface|null $value,
    ) {
    }
}
