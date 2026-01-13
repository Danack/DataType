<?php

declare(strict_types=1);

namespace DataTypeTest\ExtractRule;

use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\ExtractRule\GetString;
use DataType\ProcessedValues;
use DataType\DataStorage\TestArrayDataStorage;

/**
 * @coversNothing
 */
class GetStringTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetString
     */
    public function testMissingGivesError()
    {
        $rule = new GetString();
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
     * @covers \DataType\ExtractRule\GetString
     */
    public function testValidation()
    {
        $expectedValue = 'John';

        $rule = new GetString();
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
        yield [null, Messages::STRING_REQUIRED_FOUND_NULL];
    }

    /**
     * @covers \DataType\ExtractRule\GetString
     * @dataProvider providesErrors
     * @param mixed $input
     * @param string $expected_error
     */
    public function testErrors(mixed $input, string $expected_error)
    {
        $index = 'foo';

        $data = [$index => $input];

        $rule = new GetString();
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
     * @covers \DataType\ExtractRule\GetString
     */
    public function testFromArrayErrors()
    {
        $index = 'foo';

        $data = [$index => [1, 2, 3]];

        $rule = new GetString();
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
     * @covers \DataType\ExtractRule\GetString
     */
    public function testFromObjectErrors()
    {
        $index = 'foo';

        $data = [$index => new \stdClass()];

        $rule = new GetString();
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
     * @covers \DataType\ExtractRule\GetString
     */
    public function testBadTypeErrors()
    {
        $rule = new GetString();
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
     * @covers \DataType\ExtractRule\GetString
     */
    public function testDescription()
    {
        $rule = new GetString();
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('string', $description->getType());
        $this->assertTrue($description->getRequired());
    }
}
