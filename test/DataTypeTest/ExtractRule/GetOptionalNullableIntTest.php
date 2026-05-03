<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\ExtractRule\GetOptionalNullableInt;
use DataType\Messages;
use DataType\Presence\Absent;
use DataType\Presence\PresentNull;
use DataType\ProcessedValues;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class GetOptionalNullableIntTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableInt
     */
    public function testMissingGivesAbsent(): void
    {
        $validationResult = (new GetOptionalNullableInt())->process(
            new ProcessedValues(),
            TestArrayDataStorage::createMissing('foo')
        );

        $this->assertNoProblems($validationResult);
        $this->assertSame(Absent::instance(), $validationResult->getValue());
        $this->assertTrue($validationResult->isFinalResult());
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableInt
     */
    public function testExplicitNullGivesPresentNull(): void
    {
        $validationResult = (new GetOptionalNullableInt())->process(
            new ProcessedValues(),
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', null)
        );

        $this->assertNoProblems($validationResult);
        $this->assertSame(PresentNull::instance(), $validationResult->getValue());
        $this->assertTrue($validationResult->isFinalResult());
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableInt
     */
    public function testParsesPresentInt(): void
    {
        $validationResult = (new GetOptionalNullableInt())->process(
            new ProcessedValues(),
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', 7)
        );

        $this->assertNoProblems($validationResult);
        $this->assertSame(7, $validationResult->getValue());
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableInt
     */
    public function testBadValueErrorsLikeCastToInt(): void
    {
        $validationResult = (new GetOptionalNullableInt())->process(
            new ProcessedValues(),
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', 'xyz')
        );

        $this->assertProblems(
            $validationResult,
            ['/foo' => Messages::INT_REQUIRED_FOUND_NON_DIGITS2]
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableInt
     */
    public function testDescription(): void
    {
        $rule = new GetOptionalNullableInt();
        $description = $this->applyRuleToDescription($rule);
        $rule->updateParamDescription($description);

        $this->assertSame('integer', $description->getType());
        $this->assertFalse($description->getRequired());
        $this->assertTrue($description->getNullAllowed());
    }
}
