<?php
declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataTypeTest\BaseTestCase;
use DataType\ProcessRule\PhpEnum;
use DataTypeTest\ProcessRule\FixtureEnum;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\ProcessedValues;
use function DataType\getEnumCaseValues;

/**
 * @coversNothing
 */
class PhpEnumTest extends BaseTestCase
{
    /**
     * @covers \DataType\ProcessRule\PhpEnum
     */
    public function testValidationWorks()
    {
        $testValue = FixtureEnum::APPLES->value;

        $rule = new PhpEnum(FixtureEnum::class);

        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            FixtureEnum::APPLES->value, $processedValues, $dataStorage
        );
        $this->assertNoProblems($validationResult);
        $this->assertSame($validationResult->getValue(), $testValue);

        $invalid_case = 'not valid';

        $validationResult = $rule->process(
            $invalid_case, $processedValues, $dataStorage
        );

        $this->assertValidationProblems(
            [['/', Messages::ENUM_MAP_UNRECOGNISED_VALUE_SINGLE]],
            $validationResult->getValidationProblems()
        );

        $this->assertValidationProblemContains(
            '/',
            $invalid_case,
            $validationResult->getValidationProblems()
        );

        $enum_values = getEnumCaseValues(FixtureEnum::class);
        foreach ($enum_values as $enum_value) {
            $this->assertValidationProblemContains(
                '/',
                $enum_value,
                $validationResult->getValidationProblems()
            );
        }
    }


    /**
     * @covers \DataType\ProcessRule\PhpEnum
     */
    public function testDescription()
    {
        $rule = new PhpEnum(FixtureEnum::class);
        $description = $this->applyRuleToDescription($rule);
    }
}
