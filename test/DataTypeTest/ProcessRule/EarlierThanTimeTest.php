<?php

declare(strict_types = 1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\OpenApi\OpenApiV300ParamDescription;
use DataType\ProcessedValues;
use DataType\ProcessRule\EarlierThanTime;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class EarlierThanTimeTest extends BaseTestCase
{

    /**
     * @covers \DataType\ProcessRule\EarlierThanTime
     */
    public function testWorks()
    {
        $value = new \DateTime('2000-01-01');

        $processedValues = createProcessedValuesFromArray([]);
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('newtime', $value);

        $compareTime = new \DateTime('2001-01-01');
        $rule = new EarlierThanTime($compareTime);
        $validationResult = $rule->process($value, $processedValues, $dataStorage);

        $this->assertNoProblems($validationResult);

        $this->assertSame($value, $validationResult->getValue());
        $this->assertFalse($validationResult->isFinalResult());
    }

    public function providesErrorsCorrectly()
    {
        yield ['2020-01-01', '2001-01-01'];
        yield ['2020-01-01 12:00:00', '2020-01-01 12:00:00'];
    }

    /**
     * @covers \DataType\ProcessRule\EarlierThanTime
     * @dataProvider providesErrorsCorrectly
     * @param string $input_time
     * @param string $boundary_time
     */
    public function testErrorsCorrectly($input_time, $boundary_time)
    {
        $value = new \DateTime($input_time);

        $processedValues = createProcessedValuesFromArray([]);
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('newtime', $value);

        $compareTime = new \DateTime($boundary_time);
        $rule = new EarlierThanTime($compareTime);
        $validationResult = $rule->process($value, $processedValues, $dataStorage);

        $this->assertValidationProblemRegexp(
            '/newtime',
            Messages::TIME_MUST_BE_BEFORE_TIME,
            $validationResult->getValidationProblems()
        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertTrue($validationResult->isFinalResult());
    }


    /**
     * @covers \DataType\ProcessRule\EarlierThanTime
     */
    public function testPreviousTimeWrongType()
    {
        $value = new \stdClass();

        $processedValues = createProcessedValuesFromArray([]);
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('newtime', $value);

        $compareTime = new \DateTime('2000-01-01');
        $rule = new EarlierThanTime($compareTime);
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
     * @covers \DataType\ProcessRule\EarlierThanTime
     */
    public function testFormatting()
    {
        $compareTime = new \DateTime('2000-01-01');
        $rule = new EarlierThanTime($compareTime);

        $this->assertSame(
            $rule->getCompareTimeString(),
            $compareTime->format(\DateTime::RFC3339)
        );
    }

    /**
     * @covers \DataType\ProcessRule\EarlierThanTime
     */
    public function testDescription()
    {
        $compareTime = new \DateTime('2000-01-01');

        $rule = new EarlierThanTime($compareTime);
        $description = $this->applyRuleToDescription($rule);

        $this->assertNotNull($description->getDescription());
        $this->assertStringMatchesTemplateString(
            Messages::TIME_MUST_BE_BEFORE_TIME,
            $description->getDescription()
        );

        $this->assertStringContainsString(
            $rule->getCompareTimeString(),
            $description->getDescription()
        );
    }
}
