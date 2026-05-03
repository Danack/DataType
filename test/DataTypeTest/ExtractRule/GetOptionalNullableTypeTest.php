<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\ExtractRule\GetOptionalNullableType;
use DataType\Messages;
use DataType\Presence\Absent;
use DataType\Presence\PresentNull;
use DataType\ProcessedValues;
use DataTypeTest\BaseTestCase;
use DataTypeTestFixture\Integration\ReviewScore;

/**
 * @coversNothing
 */
class GetOptionalNullableTypeTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableType
     */
    public function testMissingGivesAbsent(): void
    {
        $rule = GetOptionalNullableType::fromClass(ReviewScore::class);

        $validationResult = $rule->process(
            new ProcessedValues(),
            TestArrayDataStorage::createMissing('review')
        );

        $this->assertNoErrors($validationResult);
        $this->assertSame(Absent::instance(), $validationResult->getValue());
        $this->assertTrue($validationResult->isFinalResult());
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableType
     */
    public function testExplicitNullGivesPresentNull(): void
    {
        $rule = GetOptionalNullableType::fromClass(ReviewScore::class);

        $validationResult = $rule->process(
            new ProcessedValues(),
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('review', null)
        );

        $this->assertSame(PresentNull::instance(), $validationResult->getValue());
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableType
     */
    public function testDecodesNestedObject(): void
    {
        $data = ['score' => 5, 'comment' => 'Hello world'];
        $rule = GetOptionalNullableType::fromClass(ReviewScore::class);

        $storage = TestArrayDataStorage::fromArray(['review' => $data])
            ->moveKey('review');

        $validationResult = $rule->process(
            new ProcessedValues(),
            $storage
        );

        $this->assertNoErrors($validationResult);
        $item = $validationResult->getValue();
        $this->assertInstanceOf(ReviewScore::class, $item);
        /** @var ReviewScore $item */
        $this->assertSame(5, $item->getScore());
        $this->assertSame('Hello world', $item->getComment());
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableType
     */
    public function testNestedValidationStillRuns(): void
    {
        $data = ['score' => 5, 'typo' => 'Hello world'];
        $rule = GetOptionalNullableType::fromClass(ReviewScore::class);

        $storage = TestArrayDataStorage::fromArray(['review' => $data])
            ->moveKey('review');

        $validationResult = $rule->process(
            new ProcessedValues(),
            $storage
        );

        $this->assertProblems(
            $validationResult,
            ['/review/comment' => Messages::VALUE_NOT_SET]
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableType
     */
    public function testFromClassAndRules(): void
    {
        $data = ['score' => 5, 'comment' => 'Hello world'];
        $rule = GetOptionalNullableType::fromClassAndRules(
            ReviewScore::class,
            ReviewScore::getInputTypes()
        );

        $storage = TestArrayDataStorage::fromArray(['review' => $data])
            ->moveKey('review');

        $validationResult = $rule->process(
            new ProcessedValues(),
            $storage
        );

        $this->assertNoErrors($validationResult);
        $this->assertInstanceOf(ReviewScore::class, $validationResult->getValue());
    }

    /**
     * @covers \DataType\ExtractRule\GetOptionalNullableType
     */
    public function testDescription(): void
    {
        $rule = GetOptionalNullableType::fromClass(ReviewScore::class);
        $description = $this->applyRuleToDescription($rule);
        $rule->updateParamDescription($description);

        $this->assertFalse($description->getRequired());
        $this->assertTrue($description->getNullAllowed());
    }
}
