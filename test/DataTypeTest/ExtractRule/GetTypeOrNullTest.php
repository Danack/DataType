<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\Messages;
use DataTypeTest\Integration\ReviewScore;
use DataTypeTest\BaseTestCase;
use DataType\ExtractRule\GetTypeOrNull;
use DataType\ProcessedValues;
use DataType\DataStorage\TestArrayDataStorage;

/**
 * @coversNothing
 */
class GetTypeOrNullTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetTypeOrNull
     */
    public function testMissingGivesError()
    {
        $rule = GetTypeOrNull::fromClass(ReviewScore::class);
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::createMissing('foo')
        );

        $this->assertProblems(
            $validationResult,
            ['/foo' => Messages::VALUE_NOT_SET]
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetTypeOrNull
     */
    public function testValidationWithNull()
    {
        $expectedValue = null;

        $rule = GetTypeOrNull::fromClass(ReviewScore::class);
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromArraySetFirstValue([$expectedValue])
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    /**
     * @covers \DataType\ExtractRule\GetTypeOrNull
     */
    public function testWorks()
    {
        $validator = new ProcessedValues();

        $data = ['score' => 5, 'comment' => 'Hello world'];

        $rule = GetTypeOrNull::fromClass(ReviewScore::class);
        $validationResult = $rule->process(
            $validator, TestArrayDataStorage::fromArray($data)
        );

        $this->assertNoErrors($validationResult);

        $item = $validationResult->getValue();
        $this->assertInstanceOf(ReviewScore::class, $item);
        /** @var ReviewScore $item */
        $this->assertSame(5, $item->getScore());
        $this->assertSame('Hello world', $item->getComment());
    }

    /**
     * @covers \DataType\ExtractRule\GetTypeOrNull
     */
    public function testWorksWithRules()
    {
        $validator = new ProcessedValues();

        $data = ['score' => 5, 'comment' => 'Hello world'];

        $rule = GetTypeOrNull::fromClassAndRules(
            ReviewScore::class,
            ReviewScore::getInputTypes()
        );
        $validationResult = $rule->process(
            $validator, TestArrayDataStorage::fromArray($data)
        );

        $this->assertNoErrors($validationResult);

        $item = $validationResult->getValue();
        $this->assertInstanceOf(ReviewScore::class, $item);
        /** @var ReviewScore $item */
        $this->assertSame(5, $item->getScore());
        $this->assertSame('Hello world', $item->getComment());
    }

    /**
     * @covers \DataType\ExtractRule\GetTypeOrNull
     */
    public function testErrors()
    {
        $validator = new ProcessedValues();

        $data = ['score' => 5, 'typo' => 'Hello world'];

        $rule = GetTypeOrNull::fromClass(ReviewScore::class);
        $validationResult = $rule->process(
            $validator, TestArrayDataStorage::fromArray($data)
        );

        $this->assertProblems(
            $validationResult,
            ['/comment' => 'Value not set.']
        );

        $this->assertNull($validationResult->getValue());
    }

    /**
     * @covers \DataType\ExtractRule\GetTypeOrNull
     */
    public function testDescription()
    {
        $rule = GetTypeOrNull::fromClass(ReviewScore::class);
        $description = $this->applyRuleToDescription($rule);
        $rule->updateParamDescription($description);
        $this->assertTrue($description->getRequired());
        $this->assertTrue($description->getNullAllowed());
    }
}
