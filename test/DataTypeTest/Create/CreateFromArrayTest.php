<?php

declare(strict_types = 1);

namespace DataTypeTest\Create;

use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataTypeTest\Integration\IntArrayParams;

/**
 * @coversNothing
 */
class CreateFromArrayTest extends BaseTestCase
{
    /**
     * @covers \DataType\Create\CreateFromArray
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

    /**
     * @covers \DataTypeTest\Integration\IntArrayParams
     */
    public function testBadInt()
    {
        $name = 'John';
        $values = [1, 2, "3 bananas", 4];
        $data = [
            'name' => $name,
            'counts' => $values
        ];

        [$intArrayParams, $errors] = IntArrayParams::createOrErrorFromArray($data);

        $this->assertNull($intArrayParams);
        $this->assertValidationErrorCount(1, $errors);

        $this->assertValidationProblem(
            '/counts/2',
            Messages::INT_REQUIRED_FOUND_NON_DIGITS2,
            $errors
        );
    }
}
