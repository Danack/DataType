<?php

declare(strict_types=1);

namespace DataTypeTest\Exception\Validator;

use DataTypeTest\BaseTestCase;
use DataType\Value\OrderElement;
use DataType\Value\Ordering;

/**
 * @coversNothing
 */
class OrderingTest extends BaseTestCase
{
    /**
     * @covers \DataType\Value\OrderElement
     * @covers \DataType\Value\Ordering
     */
    public function testBasic()
    {
        $name = 'foo';
        $order = 'asc';

        $orderElment = new OrderElement($name, $order);
        $this->assertEquals($name, $orderElment->getName());
        $this->assertEquals($order, $orderElment->getOrder());

        $ordering = new Ordering([$orderElment]);
        $this->assertEquals([$orderElment], $ordering->getOrderElements());

        $expectedOrderArray = [
            $name => $order
        ];

        $this->assertEquals($expectedOrderArray, $ordering->toOrderArray());
    }
}
