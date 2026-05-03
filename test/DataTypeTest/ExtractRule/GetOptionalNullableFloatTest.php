<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\ExtractRule\GetOptionalNullableFloat;
use DataType\Messages;
use DataType\Presence\Absent;
use DataType\Presence\PresentNull;
use DataType\ProcessedValues;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class GetOptionalNullableFloatTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableFloat
     */
    public function testMissingGivesAbsent(): void
    {
        $validationResult = (new GetOptionalNullableFloat())->process(
            new ProcessedValues(),
            TestArrayDataStorage::createMissing('k')
        );

        $this->assertNoProblems($validationResult);
        $this->assertSame(Absent::instance(), $validationResult->getValue());
        $this->assertTrue($validationResult->isFinalResult());
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableFloat
     */
    public function testExplicitNullGivesPresentNull(): void
    {
        $validationResult = (new GetOptionalNullableFloat())->process(
            new ProcessedValues(),
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('k', null)
        );

        $this->assertSame(PresentNull::instance(), $validationResult->getValue());
        $this->assertTrue($validationResult->isFinalResult());
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableFloat
     */
    public function testParsesPresentFloat(): void
    {
        $validationResult = (new GetOptionalNullableFloat())->process(
            new ProcessedValues(),
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('k', '1.5')
        );

        $this->assertNoProblems($validationResult);
        $this->assertEqualsWithDelta(1.5, $validationResult->getValue(), 1e-9);
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableFloat
     */
    public function testBadValueErrors(): void
    {
        $validationResult = (new GetOptionalNullableFloat())->process(
            new ProcessedValues(),
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('k', 'not-a-float')
        );

        $this->assertProblems(
            $validationResult,
            ['/k' => Messages::FLOAT_REQUIRED]
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableFloat
     */
    public function testDescription(): void
    {
        $rule = new GetOptionalNullableFloat();
        $description = $this->applyRuleToDescription($rule);
        $rule->updateParamDescription($description);

        $this->assertSame('number', $description->getType());
        $this->assertFalse($description->getRequired());
        $this->assertTrue($description->getNullAllowed());
    }
}
