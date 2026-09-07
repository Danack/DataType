<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\ProcessedValues;
use DataType\ProcessRule\ExactLength;
use DataTypeTest\BaseTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @coversNothing
 */
class ExactLengthTest extends BaseTestCase
{
    public function testValidationWorks()
    {
        $maxLength = 40;
        $string = '7c4a8d2e9f1b6a3c5d8e0f2a4b6c8d1e3f5a7b9c';

        $rule = new ExactLength($maxLength);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $string,
            $processedValues,
            $dataStorage
        );

        $this->assertNoProblems($validationResult);
    }

    
    public static function provideExactLengthErrors()
    {
        $maxLength = 40;

        $overLengthString = str_repeat('a', $maxLength + 1);
        $overLength = str_repeat('a', $maxLength + 1);
        $underLength = str_repeat('a', $maxLength - 1);

        yield [$maxLength, $overLengthString];
        yield [$maxLength, $overLength];
        yield [$maxLength, $underLength];
    }

    /**
     * @covers \DataType\ProcessRule\MaxLength
     */
    #[DataProvider('provideExactLengthErrors')]
    public function testErrors(int $maxLength, string $string): void
    {
        $rule = new ExactLength($maxLength);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $string);
        $validationResult = $rule->process(
            $string,
            $processedValues,
            $dataStorage
        );

        // TODO - replace this with text comparison.
        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::STRING_EXACT_LENGTH,
            $validationResult->getValidationProblems()
        );
    }


    /**
     * @covers \DataType\ProcessRule\MaxLength
     */
    public function testDescription()
    {
        $minLength = $maxLength = 20;
        $rule = new ExactLength($maxLength);
        $description = $this->applyRuleToDescription($rule);
        $this->assertSame($minLength, $description->getMinLength());
        $this->assertSame($maxLength, $description->getMaxLength());
    }
}
