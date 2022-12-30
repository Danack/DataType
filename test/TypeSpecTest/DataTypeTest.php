<?php

declare(strict_types = 1);

namespace TypeSpecTest;

use TypeSpec\ExtractRule\GetInt;
use TypeSpec\DataType;
use TypeSpec\DataStorage\TestArrayDataStorage;
use TypeSpec\ExtractRule\GetString;
use TypeSpec\ProcessRule\RangeIntValue;

/**
 * @coversNothing
 */
class DataTypeTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\DataType
     */
    public function testWorks()
    {
        $name = 'foo';
        $getIntRule = new GetInt();
        $processRule = new RangeIntValue(10, 20);

        $dataType = new DataType(
            $name,
            $getIntRule,
            $processRule
        );

        $this->assertSame($name, $dataType->getName());
        $this->assertSame($getIntRule, $dataType->getExtractRule());
        $this->assertSame([$processRule], $dataType->getProcessRules());
    }

    /**
     * @covers \TypeSpec\DataType
     */
    public function testTargetName()
    {
        $dataType = new DataType(
            'bar',
            new GetString()
        );

        $this->assertSame($dataType->getTargetParameterName(), 'bar');
        $dataType->setTargetParameterName('John');
        $this->assertSame($dataType->getTargetParameterName(), 'John');
    }
}
