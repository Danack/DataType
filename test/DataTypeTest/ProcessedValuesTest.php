<?php

declare(strict_types = 1);

namespace DataTypeTest;

use DataType\ProcessedValues;
use DataType\Exception\LogicExceptionData;
use DataType\InputType;
use DataType\ExtractRule\GetStringOrDefault;
use DataType\ProcessedValue;

/**
 * @coversNothing
 */
class ProcessedValuesTest extends BaseTestCase
{
    /**
     * @covers \DataType\ProcessedValues
     */
    public function testWorks()
    {
        $data = [
            'foo' => 'bar',
            'zebransky' => 'zoqfotpik'
        ];

        $processedValues = createProcessedValuesFromArray($data);
        $this->assertSame($data, $processedValues->getAllValues());
        $this->assertSame('bar', $processedValues->getValue('foo'));

        $this->assertTrue($processedValues->hasValue('foo'));
        $this->assertFalse($processedValues->hasValue('bad_name'));
    }

    /**
     * @covers \DataType\ProcessedValues
     */
    public function testMissingGivesException()
    {
        $processedValues = createProcessedValuesFromArray([]);

        $this->expectException(LogicExceptionData::class);
        $this->expectExceptionMessageMatchesTemplateString(LogicExceptionData::MISSING_VALUE);
        $processedValues->getValue('john');
    }

    /**
     * @covers \DataType\ProcessedValues
     */
    public function testBadArrayException()
    {
        $this->expectException(LogicExceptionData::class);
        $this->expectExceptionMessageMatchesTemplateString(LogicExceptionData::ONLY_PROCESSED_VALUES);
        $processedValues = ProcessedValues::fromArray(['foo']);
    }


    /**
     * @covers \DataType\ProcessedValues
     */
    public function testGetCorrectTarget()
    {
        $inputParameter = new InputType(
            'background_color',
            new GetStringOrDefault('red')
        );

        $inputParameter->setTargetParameterName('backgroundColor');
        $processedValue = new ProcessedValue($inputParameter, 'red');
        $processedValues = ProcessedValues::fromArray([$processedValue]);

        [$value_for_target, $available] = $processedValues->getValueForTargetProperty('backgroundColor');
        $this->assertTrue($available);
        $this->assertSame('red', $value_for_target);
    }

    /**
     * @covers \DataType\ProcessedValues
     */
    public function testCoverage()
    {
        $processedValues = ProcessedValues::fromArray([]);
        $this->assertSame(0, $processedValues->getCount());

        [$value, $found] = $processedValues->getValueForTargetProperty('foo');
        $this->assertNull($value);
        $this->assertFalse($found);
        $this->assertEmpty($processedValues->getProcessedValues());
    }
}
