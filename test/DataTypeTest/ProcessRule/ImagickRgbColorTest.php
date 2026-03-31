<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\Exception\InvalidRulesExceptionData;
use DataType\ProcessedValues;
use DataType\ProcessRule\ImagickIsRgbColor;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class ImagickRgbColorTest extends BaseTestCase
{

    public static function provideTestWorks()
    {
        yield ["DarkSeaGreen3"];
        yield ["DodgerBlue2"];
        yield ['rgb(100,100,100)'];
    }

    /**
     * @covers \DataType\ProcessRule\ImagickIsRgbColor
     */
    #[DataProvider('provideTestWorks')]
    public function testWorks(string $testValue)
    {
        $rule = new ImagickIsRgbColor();
        $processedValues = new ProcessedValues();

        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);

        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($testValue, $validationResult->getValue());
    }

    /**
     * @covers \DataType\ProcessRule\ImagickIsRgbColor
     */
    public function testOnlyString()
    {
        $testValue = 15;

        $rule = new ImagickIsRgbColor();
        $processedValues = new ProcessedValues();

        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);

        $this->expectException(InvalidRulesExceptionData::class);
        $this->expectExceptionMessageMatchesTemplateString(
            \DataType\Messages::BAD_TYPE_FOR_STRING_PROCESS_RULE
        );

        $rule->process(
            $testValue, $processedValues, $dataStorage
        );
    }

    /**
     * @covers \DataType\ProcessRule\ImagickIsRgbColor
     */
    public function testDescription()
    {
        $rule = new ImagickIsRgbColor();
        $description = $this->applyRuleToDescription($rule);
        $this->assertSame('color', $description->getFormat());
    }
}
