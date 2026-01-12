<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\OpenApi\OpenApiV300ParamDescription;
use DataType\ProcessRule\EarlierThanParam;
use DataTypeTest\BaseTestCase;
use DataType\ProcessedValues;

/**
 * @coversNothing
 */
class EarlierThanParamTest extends BaseTestCase
{
    /**
     * @covers \DataType\ProcessRule\EarlierThanParam
     */
    public function testWorks()
    {
        $otherTime = \DateTimeImmutable::createFromFormat(
            \DateTime::RFC3339,
            '2002-10-03T10:10:00-05:00'
        );

        $value = \DateTimeImmutable::createFromFormat(
            \DateTime::RFC3339,
            '2002-10-03T10:00:00-05:00'
        );

        $processedValues = createProcessedValuesFromArray(['foo' => $otherTime]);
        $dataStorage = TestArrayDataStorage::fromArray([]);

        $rule = new EarlierThanParam('foo', 10);

        $validationResult = $rule->process($value, $processedValues, $dataStorage);
        $this->assertNoProblems($validationResult);

        $this->assertSame($value, $validationResult->getValue());
        $this->assertFalse($validationResult->isFinalResult());
    }


    /**
     * @covers \DataType\ProcessRule\EarlierThanParam
     */
    public function testErrorsCorrectly()
    {
        $otherTime = \DateTimeImmutable::createFromFormat(
            \DateTime::RFC3339,
            '2002-10-03T10:00:00-05:00'
        );

        $value = \DateTimeImmutable::createFromFormat(
            \DateTime::RFC3339,
            '2002-10-03T09:51:00-05:00'
        );

        $processedValues = createProcessedValuesFromArray(['foo' => $otherTime]);
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition(
            'newtime',
            $value
        );

        $rule = new EarlierThanParam('foo', 10);

        $validationResult = $rule->process($value, $processedValues, $dataStorage);
        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertTrue($validationResult->isFinalResult());
        $this->assertValidationProblemRegexp(
            '/newtime',
            Messages::TIME_MUST_BE_X_MINUTES_BEFORE_PARAM_ERROR,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \DataType\ProcessRule\EarlierThanParam
     */
    public function testInvalidMinutes()
    {
        $this->expectExceptionMessage(Messages::MINUTES_MUST_BE_GREATER_THAN_ZERO);
        new EarlierThanParam('foo', -5);
    }

    /**
     * @covers \DataType\ProcessRule\EarlierThanParam
     */
    public function testMissing()
    {
        $value = \DateTimeImmutable::createFromFormat(
            \DateTime::RFC3339,
            '2002-10-03T10:00:00-05:00'
        );
        $processedValues = createProcessedValuesFromArray([]);
        $dataStorage = TestArrayDataStorage::fromArray([]);
        $dataStorage = $dataStorage->moveKey('foo');

        $rule = new EarlierThanParam('foo', 0);
        $validationResult = $rule->process($value, $processedValues, $dataStorage);

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ERROR_NO_PREVIOUS_PARAMETER,
            $validationResult->getValidationProblems()
        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertTrue($validationResult->isFinalResult());
    }




    /**
     * @covers \DataType\ProcessRule\EarlierThanParam
     */
    public function testPreviousTimeWrongType()
    {
        $value = \DateTimeImmutable::createFromFormat(
            \DateTime::RFC3339,
            '2002-10-03T10:00:00-05:00'
        );

        $processedValues = createProcessedValuesFromArray(['foo' => 'John']);
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('newtime', $value);

        $rule = new EarlierThanParam('foo', 0);

        $validationResult = $rule->process($value, $processedValues, $dataStorage);


        $this->assertValidationProblemRegexp(
            '/newtime',
            Messages::PREVIOUS_TIME_MUST_BE_DATETIMEINTERFACE,
            $validationResult->getValidationProblems()
        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertTrue($validationResult->isFinalResult());
    }


    /**
     * @covers \DataType\ProcessRule\EarlierThanParam
     */
    public function testCurrentTimeWrongType()
    {
        $value = 'John';

        $previousTime = \DateTimeImmutable::createFromFormat(
            \DateTime::RFC3339,
            '2002-10-02T10:00:00-05:00'
        );

        $processedValues = createProcessedValuesFromArray(['foo' => $previousTime]);
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('newtime', $value);

        $rule = new EarlierThanParam('foo', 0);

        $validationResult = $rule->process($value, $processedValues, $dataStorage);

        $this->assertValidationProblemRegexp(
            '/newtime',
            Messages::CURRENT_TIME_MUST_BE_DATETIMEINTERFACE,
            $validationResult->getValidationProblems()
        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertTrue($validationResult->isFinalResult());
    }

    /**
     * @covers \DataType\ProcessRule\EarlierThanParam
         */
    public function testErrorsCorrect()
    {
        $afterTime = \DateTimeImmutable::createFromFormat(
            \DateTime::RFC3339,
            '2002-10-03T10:09:00-05:00'
        );

        $value = \DateTimeImmutable::createFromFormat(
            \DateTime::RFC3339,
            '2002-10-03T10:00:00-05:00'
        );

        $processedValues = createProcessedValuesFromArray(['foo' => $afterTime]);
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('newtime', $value);

        $rule = new EarlierThanParam('foo', 10);

        $validationResult = $rule->process($value, $processedValues, $dataStorage);

        $this->assertValidationProblemRegexp(
            '/newtime',
            Messages::TIME_MUST_BE_X_MINUTES_BEFORE_PARAM_ERROR,
            $validationResult->getValidationProblems()
        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertTrue($validationResult->isFinalResult());
    }


    /**
     * @covers \DataType\ProcessRule\EarlierThanParam
     */
    public function testDescription()
    {
        $parameterName = 'foo';

        $rule = new EarlierThanParam($parameterName, 5);
        $description = $this->applyRuleToDescription($rule);

        $this->assertNotNull($description->getDescription());
        $this->assertStringMatchesTemplateString(
            Messages::TIME_MUST_BE_X_MINUTES_BEFORE_PARAM,
            $description->getDescription()
        );

        $this->assertStringContainsString($parameterName, $description->getDescription());
    }
}
