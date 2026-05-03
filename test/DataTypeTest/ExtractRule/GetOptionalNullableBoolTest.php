<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\ExtractRule\GetOptionalNullableBool;
use DataType\Messages;
use DataType\Presence\Absent;
use DataType\Presence\PresentNull;
use DataType\ProcessedValues;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class GetOptionalNullableBoolTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableBool
     */
    public function testMissingGivesAbsent(): void
    {
        $validationResult = (new GetOptionalNullableBool())->process(
            new ProcessedValues(),
            TestArrayDataStorage::createMissing('flag')
        );

        $this->assertSame(Absent::instance(), $validationResult->getValue());
        $this->assertTrue($validationResult->isFinalResult());
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableBool
     */
    public function testExplicitNullGivesPresentNull(): void
    {
        $validationResult = (new GetOptionalNullableBool())->process(
            new ProcessedValues(),
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('flag', null)
        );

        $this->assertSame(PresentNull::instance(), $validationResult->getValue());
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableBool
     */
    public function testParsesTrue(): void
    {
        $validationResult = (new GetOptionalNullableBool())->process(
            new ProcessedValues(),
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('flag', 'true')
        );

        $this->assertNoProblems($validationResult);
        $this->assertTrue($validationResult->getValue());
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableBool
     */
    public function testInvalidValueErrors(): void
    {
        $validationResult = (new GetOptionalNullableBool())->process(
            new ProcessedValues(),
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('flag', 'maybe')
        );

        $this->assertProblems(
            $validationResult,
            ['/flag' => Messages::ERROR_BOOL_BAD_STRING]
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableBool
     */
    public function testDescription(): void
    {
        $rule = new GetOptionalNullableBool();
        $description = $this->applyRuleToDescription($rule);
        $rule->updateParamDescription($description);

        $this->assertSame('boolean', $description->getType());
        $this->assertFalse($description->getRequired());
        $this->assertTrue($description->getNullAllowed());
    }
}
