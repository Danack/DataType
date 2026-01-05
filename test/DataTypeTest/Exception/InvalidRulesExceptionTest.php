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
     * @covers \DataType\Exception\InvalidRulesExceptionData
     * @dataProvider providesInvalidRulesException
     */
    public function testInvalidRulesException($badValue, $badTypeString)
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
     * @covers \DataType\Exception\InvalidRulesExceptionData
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
}
