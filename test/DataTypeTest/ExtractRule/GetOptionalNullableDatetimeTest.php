<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\ExtractRule\GetOptionalNullableDatetime;
use DataType\Messages;
use DataType\Presence\Absent;
use DataType\Presence\PresentNull;
use DataType\ProcessedValues;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class GetOptionalNullableDatetimeTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableDatetime
     */
    public function testMissingGivesAbsent(): void
    {
        $validationResult = (new GetOptionalNullableDatetime())->process(
            new ProcessedValues(),
            TestArrayDataStorage::createMissing('at')
        );

        $this->assertSame(Absent::instance(), $validationResult->getValue());
        $this->assertTrue($validationResult->isFinalResult());
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableDatetime
     */
    public function testExplicitNullGivesPresentNull(): void
    {
        $validationResult = (new GetOptionalNullableDatetime())->process(
            new ProcessedValues(),
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('at', null)
        );

        $this->assertSame(PresentNull::instance(), $validationResult->getValue());
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableDatetime
     */
    public function testParsesValidString(): void
    {
        $input = '2002-10-02T10:00:00-05:00';
        $expected = \DateTimeImmutable::createFromFormat(\DateTime::RFC3339, $input);

        $validationResult = (new GetOptionalNullableDatetime([\DateTime::RFC3339]))
            ->process(
                new ProcessedValues(),
                TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('at', $input)
            );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($expected, $validationResult->getValue());
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableDatetime
     */
    public function testBadStringUsesSameMessageAsGetDatetime(): void
    {
        $validationResult = (new GetOptionalNullableDatetime([\DateTime::RFC3339]))
            ->process(
                new ProcessedValues(),
                TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('at', 'not a date')
            );

        $this->assertProblems(
            $validationResult,
            ['/at' => Messages::ERROR_INVALID_DATETIME]
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableDatetime
     */
    public function testDescription(): void
    {
        $rule = new GetOptionalNullableDatetime();
        $description = $this->applyRuleToDescription($rule);
        $rule->updateParamDescription($description);

        $this->assertFalse($description->getRequired());
        $this->assertSame('string', $description->getType());
        $this->assertTrue($description->getNullAllowed());
    }
}
