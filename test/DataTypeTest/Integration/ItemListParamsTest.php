<?php

declare(strict_types = 1);

namespace DataTypeTest\Integration;

use VarMap\ArrayVarMap;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class ItemListParamsTest extends BaseTestCase
{
    /**
     * @covers \DataType\ExtractRule\GetArrayOfType
     */
    public function testWorks()
    {
        $description = 'This is a test';

        $data = [
            'description' => $description,
            'items' => [
                ['score' => 20, 'comment' => 'Hello'],
                ['score' => 30, 'comment' => 'world'],
            ]
        ];

        /** @var ItemParams $itemListParams */
        [$itemListParams, $errors] = ItemParams::createOrErrorFromVarMap(
            new ArrayVarMap($data)
        );

        $this->assertNoValidationProblems($errors);

        $this->assertInstanceOf(ItemParams::class, $itemListParams);
        $this->assertSame($description, $itemListParams->getDescription());

        $items = $itemListParams->getItems();
        $this->assertCount(2, $items);

        $item1 = $items[0];
        $this->assertSame(20, $item1->getScore());
        $this->assertSame('Hello', $item1->getComment());

        $item2 = $items[1];
        $this->assertSame(30, $item2->getScore());
        $this->assertSame('world', $item2->getComment());
    }


    /**
     * @covers \DataType\ExtractRule\GetArrayOfType
     */
    public function testItemsMissing()
    {
        $description = 'This is a test';

        $data = [
            'description' => $description,
        ];

        /** @var ItemParams $itemListParams */
        [$itemListParams, $validationProblems] = ItemParams::createOrErrorFromVarMap(
            new ArrayVarMap($data)
        );

        $this->assertNull($itemListParams);
        $this->assertCount(1, $validationProblems);

        $this->assertValidationProblem(
            '/items',
            "Value must be set.",
            $validationProblems
        );
    }
}
