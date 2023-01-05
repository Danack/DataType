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

//    /**
//     * @covers \ParamsTest\Integration\IntArrayParams
//     */
//    public function testBadInt()
//    {
//        $name = 'John';
//        $values = [1, 2, "3 bananas", 4];
//        $data = [
//            'name' => $name,
//            'counts' => $values
//        ];
//
//        [$intArrayParams, $errors] = IntArrayParams::createOrErrorFromArray($data);
//
//        $this->assertNull($intArrayParams);
//        $this->assertCount(1, $errors);
//
//        $this->assertValidationProblem(
//            '/counts2',
//            "Value must contain only digits.",
//            $errors
//        );
//    }
}
