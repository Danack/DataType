<?php

declare(strict_types=1);

namespace DataTypeTest\Exception;

use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\Exception\InvalidRulesExceptionData;

/**
 * @coversNothing
 */
class InvalidRulesExceptionTest extends BaseTestCase
{

    public function providesInvalidRulesException()
    {
        yield [new \stdClass(), 'object'];
        yield [[], 'array'];
        yield [4.3, 'double'];
    }

    /**
     * @covers \DataType\Exception\InvalidRulesExceptionData::badTypeForArrayAccess
     * @dataProvider providesInvalidRulesException
     */
    public function testInvalidRulesException(mixed $badValue, string $badTypeString)
    {
        $exception = InvalidRulesExceptionData::badTypeForArrayAccess($badValue);
        $this->assertStringMatchesTemplateString(
            Messages::BAD_TYPE_FOR_ARRAY_ACCESS,
            $exception->getMessage()
        );

        $this->assertStringContainsString($badTypeString, $exception->getMessage());

        $this->assertSame(0, $exception->getCode());
    }

    /**
     * @covers \DataType\Exception\InvalidRulesExceptionData::expectsStringForProcessing
     */
    public function testExpectsStringForProcessing()
    {
        $exception = InvalidRulesExceptionData::expectsStringForProcessing('some_class_name');
        $this->assertStringMatchesTemplateString(
            Messages::BAD_TYPE_FOR_STRING_PROCESS_RULE,
            $exception->getMessage()
        );

        $this->assertStringContainsString('some_class_name', $exception->getMessage());

        $this->assertSame(0, $exception->getCode());
    }

    /**
     * @covers \DataType\Exception\InvalidRulesExceptionData::expectsIntForProcessing
     */
    public function testExpectsIntForProcessing(): void
    {
        $exception = InvalidRulesExceptionData::expectsIntForProcessing('SomeIntRule');
        $this->assertStringMatchesTemplateString(
            Messages::BAD_TYPE_FOR_INT_PROCESS_RULE,
            $exception->getMessage()
        );
        $this->assertStringContainsString('SomeIntRule', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
    }

    /**
     * @covers \DataType\Exception\InvalidRulesExceptionData::expectsFloatForProcessing
     */
    public function testExpectsFloatForProcessing(): void
    {
        $exception = InvalidRulesExceptionData::expectsFloatForProcessing('SomeFloatRule');
        $this->assertStringMatchesTemplateString(
            Messages::BAD_TYPE_FOR_FLOAT_PROCESS_RULE,
            $exception->getMessage()
        );
        $this->assertStringContainsString('SomeFloatRule', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
    }
}
