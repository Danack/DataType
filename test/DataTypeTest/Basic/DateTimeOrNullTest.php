<?php

declare(strict_types=1);

namespace DataTypeTest\Basic;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\Basic\DateTimeOrNull;
use DataType\Create\CreateFromVarMap;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;
use DataTypeTestFixture\Basic\DateTimeOrNullFixture;

/**
 * @covers \DataType\Basic\DateTimeOrNull
 */
class DateTimeOrNullTest extends BaseTestCase
{
    /**
     * @return \Generator<string, array{array<string, mixed>, bool}>  true = expect DateTimeInterface, false = expect null
     */
    public static function provides_works_parses_input_to_expected(): \Generator
    {
        yield 'valid datetime' => [['when' => '2002-10-02T10:00:00-05:00'], true];
        yield 'null value' => [['when' => null], false];
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('provides_works_parses_input_to_expected')]
    public function test_works_parses_input_to_expected(array $data, bool $expectDateTime): void
    {
        $result = DateTimeOrNullFixture::createFromVarMap(new ArrayVarMap($data));
        if ($expectDateTime) {
            $this->assertInstanceOf(\DateTimeInterface::class, $result->value);
        } else {
            $this->assertNull($result->value);
        }
    }

    /**
     * @return \Generator<string, array{array<string, mixed>, string, string}>
     */
    public static function provides_fails_with_validation_error(): \Generator
    {
        yield 'missing parameter' => [[], '/when', Messages::VALUE_NOT_SET];
        yield 'invalid datetime' => [['when' => 'not a date'], '/when', Messages::ERROR_INVALID_DATETIME];
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('provides_fails_with_validation_error')]
    public function test_fails_with_validation_error(array $data, string $path, string $messagePattern): void
    {
        try {
            DateTimeOrNullFixture::createFromVarMap(new ArrayVarMap($data));
            $this->fail('Expected ValidationException was not thrown.');
        } catch (\DataType\Exception\ValidationException $ve) {
            $this->assertValidationProblemRegexp($path, $messagePattern, $ve->getValidationProblems());
        }
    }
}
