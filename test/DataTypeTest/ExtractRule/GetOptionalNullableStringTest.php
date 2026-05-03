<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\DataStorage\ArrayDataStorage;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\ExtractRule\GetOptionalNullableString;
use DataType\Messages;
use DataType\Presence\Absent;
use DataType\Presence\PresentNull;
use DataType\ProcessedValues;
use DataTypeTest\BaseTestCase;
use DataTypeTestFixture\TriState\PatchNicknameParams;
use DataTypeTestFixture\TriState\PatchNicknameStrictLength;
use DataType\Exception\ValidationException;
use function DataType\create;

/**
 * @coversNothing
 */
class GetOptionalNullableStringTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableString
     */
    public function testMissingGivesAbsent(): void
    {
        $rule = new GetOptionalNullableString();

        $validationResult = $rule->process(
            new ProcessedValues(),
            TestArrayDataStorage::createMissing('foo')
        );

        $this->assertNoProblems($validationResult);
        $this->assertSame(Absent::instance(), $validationResult->getValue());
        $this->assertTrue($validationResult->isFinalResult());
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableString
     */
    public function testExplicitNullGivesPresentNull(): void
    {
        $rule = new GetOptionalNullableString();

        $validationResult = $rule->process(
            new ProcessedValues(),
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', null)
        );

        $this->assertNoProblems($validationResult);
        $this->assertSame(PresentNull::instance(), $validationResult->getValue());
        $this->assertTrue($validationResult->isFinalResult());
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableString
     */
    public function testStringValueNotFinalUntilProcessed(): void
    {
        $rule = new GetOptionalNullableString();

        $validationResult = $rule->process(
            new ProcessedValues(),
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', 'bar')
        );

        $this->assertNoProblems($validationResult);
        $this->assertSame('bar', $validationResult->getValue());
        $this->assertFalse($validationResult->isFinalResult());
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableString
     */
    public function testBadTypeErrors(): void
    {
        $rule = new GetOptionalNullableString();

        $validationResult = $rule->process(
            new ProcessedValues(),
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', 99)
        );

        $this->assertProblems(
            $validationResult,
            ['/foo' => Messages::STRING_EXPECTED]
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableString
     */
    public function testDescription(): void
    {
        $rule = new GetOptionalNullableString();
        $description = $this->applyRuleToDescription($rule);
        $rule->updateParamDescription($description);

        $this->assertSame('string', $description->getType());
        $this->assertFalse($description->getRequired());
        $this->assertTrue($description->getNullAllowed());
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableString
     */
    public function testEndToEndCreatePatchNicknameDto(): void
    {
        $missing = create(
            PatchNicknameParams::class,
            PatchNicknameParams::getInputTypes(),
            ArrayDataStorage::fromArray([])
        );

        $this->assertSame(Absent::instance(), $missing->getNickname());

        $nullInput = create(
            PatchNicknameParams::class,
            PatchNicknameParams::getInputTypes(),
            ArrayDataStorage::fromArray(['nickname' => null])
        );

        $this->assertSame(PresentNull::instance(), $nullInput->getNickname());

        $named = create(
            PatchNicknameParams::class,
            PatchNicknameParams::getInputTypes(),
            ArrayDataStorage::fromArray(['nickname' => 'bob'])
        );

        $this->assertSame('bob', $named->getNickname());
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableString
     */
    public function testPresentStringRunsMaxLengthValidation(): void
    {
        $this->expectException(ValidationException::class);

        create(
            PatchNicknameStrictLength::class,
            PatchNicknameStrictLength::getInputTypes(),
            ArrayDataStorage::fromArray(['nickname' => 'abcd'])
        );
    }
}
