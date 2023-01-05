<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use DataType\DataStorage\TestArrayDataStorage;
use DataType\OpenApi\OpenApiV300ParamDescription;
use DataTypeTest\BaseTestCase;
use DataType\ProcessRule\CheckOnlyAllowedCharacters;
use DataType\ProcessRule\SaneCharacters;
use DataType\ProcessedValues;

/**
 * @coversNothing
 */
class CheckOnlyAllowedCharactersTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            ['a-zA-Z', 'john', null],
            ['a-zA-Z', 'johnny-5', 6],  // bad digit and hyphen
            ['a-zA-Z', 'jo  hn', 2], // bad space

            [implode(SaneCharacters::ALLOWED_CHAR_TYPES), "jo.hn", null], //punctuation is not letter or number
        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \DataType\ProcessRule\CheckOnlyAllowedCharacters
     */
    public function testValidation($validCharactersPattern, $testValue, $expectedErrorPosition)
    {
        $rule = new CheckOnlyAllowedCharacters($validCharactersPattern);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);

        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );
        if ($expectedErrorPosition !== null) {
            $this->assertValidationProblemRegexp(
                '/foo',
                \DataType\Messages::STRING_FOUND_INVALID_CHAR,
                $validationResult->getValidationProblems()
            );

            $this->assertValidationProblemRegexp(
                '/foo',
                $validCharactersPattern,
                $validationResult->getValidationProblems()
            );

            // Check the correct position is in the error message.
            $this->assertCount(1, $validationResult->getValidationProblems());
            $validationProblem = $validationResult->getValidationProblems()[0];
            $this->assertStringContainsString(
                (string)$expectedErrorPosition,
                $validationProblem->getProblemMessage()
            );
        }
        else {
            $this->assertNoProblems($validationResult);
        }
    }

    /**
     * @covers \DataType\ProcessRule\CheckOnlyAllowedCharacters
     */
    public function testDescription()
    {
        $rule = new CheckOnlyAllowedCharacters('a-zA-Z');
        $description = $this->applyRuleToDescription($rule);

//        $schema = $description->toArray();

//        type: string
//      maxLength: 10
//      pattern: ^[A-Za-z0-9]{3,10}$
    }
}
