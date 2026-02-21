<?php

declare(strict_types = 1);

namespace DataTypeTest;

use DataType\ExtractRule\FixedValue;
use DataType\InputType;
use DataType\ProcessedValue;

/**
 * @coversNothing
 */
class ProcessedValueTest extends BaseTestCase
{

        /**
     * @covers \DataType\ProcessedValue
     */
    public function testMissingGivesException()
    {
        $value = 5;
        $foo = new InputType(
            'john',
            new FixedValue($value)
        );
        $processedValues = new ProcessedValue($foo, $value);
        $this->assertSame($value, $processedValues->getValue());
        $this->assertSame($foo, $processedValues->getInputType());
    }
}
