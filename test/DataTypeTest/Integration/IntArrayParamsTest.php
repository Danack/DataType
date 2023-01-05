<?php

declare(strict_types = 1);

namespace DataTypeTest\Integration;

use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class IntArrayParamsTest extends BaseTestCase
{
    /**
     * @covers \DataTypeTest\Integration\IntArrayParams
     */
    public function testWorks()
    {
        $name = 'John';
        $values = [3, 6, 9, 12];
        $data = [
            'name' => $name,
            'counts' => $values
        ];

        $intArrayParams = IntArrayParams::createFromArray($data);

        $this->assertInstanceOf(IntArrayParams::class, $intArrayParams);
        $this->assertSame($name, $intArrayParams->getName());
        $this->assertSame($values, $intArrayParams->getCounts());
    }
}
