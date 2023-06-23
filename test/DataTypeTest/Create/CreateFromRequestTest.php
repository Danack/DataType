<?php

declare(strict_types = 1);

namespace DataTypeTest\Create;

use DataTypeTest\BaseTestCase;
use DataTypeTest\Integration\IntArrayParams;
use DataTypeTest\MockRequest;

/**
 * @coversNothing
 */
class CreateFromRequestTest extends BaseTestCase
{
    /**
     * @covers \DataType\Create\CreateFromRequest
     */
    public function testWorks()
    {
        $name = 'John';
        $values = [3, 6, 9, 12];
        $data = [
            'name' => $name,
            'counts' => $values
        ];

        $request = MockRequest::createfromQueryParams($data);
        $intArrayParams = IntArrayParams::createFromRequest($request);
        $this->assertInstanceOf(IntArrayParams::class, $intArrayParams);
        $this->assertSame($name, $intArrayParams->getName());
        $this->assertSame($values, $intArrayParams->getCounts());
    }
}
