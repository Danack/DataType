<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\ProcessedValues;
use DataType\ProcessRule\MinLength;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class MinLengthTest extends BaseTestCase
{
    public static function provideMaxLengthCases()
    {
        $minLength = 8;
        $underLengthMinString = str_repeat('a', $minLength - 1);
        $exactLengthMinString = str_repeat('a', $minLength);
        $overLengthMinString = str_repeat('a', $minLength + 1);

        // Test the edge behaviour around partially multibyte strings
        $underLengthMinWithMBString = str_repeat('a', $minLength - 2) . "\xC2\xA3";
        $exactLengthMinWithMBString = str_repeat('a', $minLength - 1) . "\xC2\xA3";
        $overLengthMinWithMBString = str_repeat('a', $minLength) . "\xC2\xA3";

        // Test the edge behaviour around strings that are only MB characters
        $underLengthMinMBStringOnly = str_repeat("\xC2\xA3", $minLength - 1);
        $exactLengthMinMBStringOnly = str_repeat("\xC2\xA3", $minLength);
        $overLengthMinMBStringOnly = str_repeat("\xC2\xA3", $minLength + 1);

        return [
//            [$minLength, $underLengthMinString, true],
            [$minLength, $exactLengthMinString],
            [$minLength, $overLengthMinString],

//            [$minLength, $underLengthMinWithMBString, true],
            [$minLength, $exactLengthMinWithMBString],
            [$minLength, $overLengthMinWithMBString],

//            [$minLength, $underLengthMinMBStringOnly, true],
            [$minLength, $exactLengthMinMBStringOnly],
            [$minLength, $overLengthMinMBStringOnly],
        ];
    }

    /**
     * @covers \DataType\ProcessRule\MinLength
     */
    #[DataProvider('provideMaxLengthCases')]
    public function testValidation(int $minLength, string $string)
    {
        $rule = new MinLength($minLength);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $string, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
    }


    public static function provideMinLengthErrors()
    {
        $minLength = 8;
        $underLengthMinString = str_repeat('a', $minLength - 1);

        // Test the edge behaviour around partially multibyte strings
        $underLengthMinWithMBString = str_repeat('a', $minLength - 2) . "\xC2\xA3";

        // Test the edge behaviour around strings that are only MB characters
        $underLengthMinMBStringOnly = str_repeat("\xC2\xA3", $minLength - 1);

        return [
            [$minLength, $underLengthMinString],
            [$minLength, $underLengthMinWithMBString],
            [$minLength, $underLengthMinMBStringOnly],
        ];
    }

    /**
     * @covers \DataType\ProcessRule\MinLength
     */
    #[DataProvider('provideMinLengthErrors')]
    public function testErrors(int $minLength, string $string)
    {
        $rule = new MinLength($minLength);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $string);
        $validationResult = $rule->process(
            $string, $processedValues, $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::STRING_TOO_SHORT,
            $validationResult->getValidationProblems()
        );

        $this->assertOneErrorAndContainsString($validationResult, (string)$minLength);
    }


    /**
     * @covers \DataType\ProcessRule\MinLength
     */
    public function testDescription()
    {
        $minLength = 20;
        $rule = new MinLength($minLength);
        $description = $this->applyRuleToDescription($rule);
        $this->assertSame($minLength, $description->getMinLength());
    }
}
