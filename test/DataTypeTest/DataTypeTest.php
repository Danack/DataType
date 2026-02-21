<?php

declare(strict_types = 1);

namespace DataTypeTest;

use DataType\ExtractRule\GetInt;
use DataType\ExtractRule\GetString;
use DataType\InputType;
use DataType\ProcessRule\RangeIntValue;

/**
 * @coversNothing
 */
class DataTypeTest extends BaseTestCase
{
    /**
     * @covers \DataType\InputType
     */
    public function testWorks()
    {
        $name = 'foo';
        $getIntRule = new GetInt();
        $processRule = new RangeIntValue(10, 20);

        $dataType = new InputType(
            $name,
            $getIntRule,
            $processRule
        );

        $this->assertSame($name, $dataType->getName());
        $this->assertSame($getIntRule, $dataType->getExtractRule());
        $this->assertSame([$processRule], $dataType->getProcessRules());
    }

    /**
     * @covers \DataType\InputType
     */
    public function testTargetName()
    {
        $dataType = new InputType(
            'bar',
            new GetString()
        );

        $this->assertSame($dataType->getTargetParameterName(), 'bar');
        $dataType->setTargetParameterName('John');
        $this->assertSame($dataType->getTargetParameterName(), 'John');
    }
}
