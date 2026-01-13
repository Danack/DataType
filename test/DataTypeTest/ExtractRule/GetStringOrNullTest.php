<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\ExtractRule\GetStringOrNull;
use DataType\ProcessedValues;
use DataType\DataStorage\TestArrayDataStorage;

/**
 * @coversNothing
 */
class GetStringOrNullTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetStringOrNull
     */
    public function testMissingGivesError()
    {
        $rule = new GetStringOrNull();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::createMissing('foo')
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::VALUE_NOT_SET,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetStringOrNull
     */
    public function testValidation()
    {
        $expectedValue = 'John';

        $rule = new GetStringOrNull();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromArraySetFirstValue([$expectedValue])
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    /**
     * @covers \DataType\ExtractRule\GetStringOrNull
     */
    public function testValidationWithNull()
    {
        $expectedValue = null;

        $rule = new GetStringOrNull();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromArraySetFirstValue([$expectedValue])
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }



    public function providesErrors()
    {
        yield [[1, 2, 3], Messages::STRING_REQUIRED_FOUND_NON_SCALAR];
    }

    /**
     * @covers \DataType\ExtractRule\GetStringOrNull
     * @dataProvider providesErrors
     * @param mixed $input
     * @param string $expected_error
     */
    public function testErrors($input, $expected_error)
    {
        $index = 'foo';

        $data = [$index => $input];

        $rule = new GetStringOrNull();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromArraySetFirstValue($data)
        );

        $this->assertValidationProblemRegexp(
            '/' . $index,
            $expected_error,
            $validationResult->getValidationProblems()
        );
    }


    /**
     * @covers \DataType\ExtractRule\GetStringOrNull
     */
    public function testFromArrayErrors()
    {
        $index = 'foo';

        $data = [$index => [1, 2, 3]];

        $rule = new GetStringOrNull();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromArraySetFirstValue($data)
        );

        $this->assertValidationProblemRegexp(
            '/' . $index,
            Messages::STRING_REQUIRED_FOUND_NON_SCALAR,
            $validationResult->getValidationProblems()
        );
    }


    /**
     * @covers \DataType\ExtractRule\GetStringOrNull
     */
    public function testFromObjectErrors()
    {
        $index = 'foo';

        $data = [$index => new \stdClass()];

        $rule = new GetStringOrNull();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromArraySetFirstValue($data)
        );

        $this->assertValidationProblemRegexp(
            '/' . $index,
            Messages::STRING_REQUIRED_FOUND_NON_SCALAR,
            $validationResult->getValidationProblems()
        );
    }


    /**
     * @covers \DataType\ExtractRule\GetStringOrNull
     */
    public function testBadTypeErrors()
    {
        $rule = new GetStringOrNull();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', 5)
        );

        $this->assertProblems(
            $validationResult,
            ['/foo' => Messages::STRING_EXPECTED]
        );
    }

    /**
     * @covers \DataType\ExtractRule\GetStringOrNull
     */
    public function testDescription()
    {
        $rule = new GetStringOrNull();
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('string', $description->getType());
        $this->assertTrue($description->getRequired());
        $this->assertTrue($description->getNullAllowed());
    }
}
