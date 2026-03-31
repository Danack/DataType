<?php

declare(strict_types=1);

namespace DataTypeTest\ProcessRule;

use PHPUnit\Framework\Attributes\DataProvider;
use DataType\DataStorage\TestArrayDataStorage;
use DataType\ProcessedValues;
use DataType\ProcessRule\NullIfEmpty;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class NullIfEmptyTest extends BaseTestCase
{
    public static function provideTestWorksCases()
    {
        return [
            ['pk_foobar', false],
            ['   .   ',   false],

            [null, true],
            ['', true],
            ['                    ', true],
        ];
    }

    /**
     * @covers \DataType\ProcessRule\NullIfEmpty
     */
    #[DataProvider('provideTestWorksCases')]
    public function testValidationWorks(string|null $testValue, bool $shouldBeNull)
    {
        $rule = new NullIfEmpty();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );
        $this->assertNoProblems($validationResult);


        if ($shouldBeNull === true) {
            $this->assertNull($validationResult->getValue());
        }
        else {
            $this->assertSame($testValue, $validationResult->getValue());
        }
    }
}
