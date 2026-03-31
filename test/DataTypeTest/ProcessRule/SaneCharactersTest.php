<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\Messages;
use DataType\ProcessedValues;
use DataType\ProcessRule\SaneCharacters;
use DataTypeTest\BaseTestCase;

// TODO - the regular expressions need documenting.
// https://json-schema.org/understanding-json-schema/reference/regular_expressions.html

/**
 * @coversNothing
 */
class SaneCharactersTest extends BaseTestCase
{
    public static function provideSuccessCases()
    {
        return [
            ["John Smith"],
            ["Basic punctuation:'\".⁋′″‴‵‶‷"],
            ["ÁGUEDA"],
            ["ALÍCIA"],
            ["☺😎😋😂"], // emoticons \u{1F600}-\u{1F64F}
            ["✅✨❕"], // Dingbats ( 2702 - 27B0 )
            ["🚅🚲🚤"], // Transport and map symbols ( 1F680 - 1F6C0 )
            ["🆕🇯🇵🉑"],    //Enclosed characters ( 24C2 - 1F251 )
            ["⁉4⃣⌛"], // Uncategorized
            ["😀😶😕"],           // Additional emoticons ( 1F600 - 1F636 )
            ["🚍🚛🚛"],         // Additional transport and map symbols
            ["🕜🐇🕝"], // Other additional symbols
        ];
    }

    public static function provideFailureCases()
    {
        return [
            ["a̧͈͖r͒͑"],
//            [" ͎a̧͈͖r̽̾̈́͒͑e"],
//            ["TO͇̹̺ͅƝ̴ȳ̳ TH̘Ë͖́̉ ͠P̯͍̭O̚​N̐Y̡ H̸̡̪̯ͨ͊̽̅̾̎Ȩ̬̩̾͛ͪ̈́̀́͘"],
//            ["C̷̙̲̝͖ͭ̏ͥͮ͟Oͮ͏̮̪̝͍M̲̖͊̒ͪͩͬ̚̚͜Ȇ̴̟̟͙̞ͩ͌͝S̨̥̫͎̭ͯ̿̔̀ͅ"],
        ];
    }

    /**
     * @covers \DataType\ProcessRule\SaneCharacters
     */
    #[DataProvider('provideSuccessCases')]
    public function testValidationSuccess(string $testValue)
    {
        $rule = new SaneCharacters();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );
        $this->assertNoProblems($validationResult);
    }

    /**
     * @covers \DataType\ProcessRule\SaneCharacters
     */
    #[DataProvider('provideFailureCases')]
    public function testValidationErrors(string $testValue)
    {
        $rule = new SaneCharacters();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);
        $validationResult = $rule->process(
            $testValue,
            $processedValues,
            $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::STRING_INVALID_COMBINING_CHARACTERS,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @return void
     * @covers \DataType\ProcessRule\SaneCharacters
     */
    public function testInvalidCharacters()
    {
        // 0x8 = backspace
        $testValue = "Hello \u{8}";

        $rule = new SaneCharacters();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition(
            'foo', $testValue
        );
        $validationResult = $rule->process(
            $testValue,
            $processedValues,
            $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::STRING_FOUND_INVALID_CHAR,
            $validationResult->getValidationProblems()
        );
    }

    public function testPositionIsCorrect()
    {
        $testValue = "danack_a̧͈͖r͒͑_more_a̧͈͖r͒͑";
        $rule = new SaneCharacters();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertCount(1, $validationResult->getValidationProblems());

        $this->assertValidationProblem(
            '/',
            "Invalid combining characters found at position 8",
            $validationResult->getValidationProblems()
        );
    }


    /**
     * @covers \DataType\ProcessRule\SaneCharacters
     */
    public function testDescription()
    {
        $rule = new SaneCharacters();
        $description = $this->applyRuleToDescription($rule);
        // TODO - check result is as expected.
    }
}
