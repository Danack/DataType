<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\ExtractRule\GetType;
use DataType\Messages;
use DataType\ProcessedValues;
use DataTypeTest\BaseTestCase;
use DataTypeTest\Integration\ReviewScore;

/**
 * @coversNothing
 */
class GetTypeTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetType
     */
    public function testWorks()
    {
        $validator = new ProcessedValues();

        $data = ['score' => 5, 'comment' => 'Hello world'];

        $rule = GetType::fromClass(ReviewScore::class);
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
     * @covers \DataType\ExtractRule\GetType
     */
    public function testWorksWithRules()
    {
        $validator = new ProcessedValues();

        $data = ['score' => 5, 'comment' => 'Hello world'];

        $rule = GetType::fromClassAndInputTypes(
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
     * @covers \DataType\ExtractRule\GetType
     */
    public function testMissingGivesError()
    {
        $rule = GetType::fromClass(ReviewScore::class);
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
     * @covers \DataType\ExtractRule\GetType
     */
    public function testErrors()
    {
        $validator = new ProcessedValues();

        $data = ['score' => 5, 'typo' => 'Hello world'];

        $rule = GetType::fromClass(ReviewScore::class);
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
     * @covers \DataType\ExtractRule\GetType
     */
    public function testDescription()
    {
        $rule = GetType::fromClass(ReviewScore::class);
        $description = $this->applyRuleToDescription($rule);
        $rule->updateParamDescription($description);
        $this->assertTrue($description->getRequired());
        // TODO - how should this behave...
    }
}
