<?php

declare(strict_types = 1);

namespace TypeSpecTest\Create;

use TypeSpecTest\BaseTestCase;
use TypeSpecTest\Integration\IntArrayParams;

/**
 * @coversNothing
 */
class CreateFromArrayTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\Create\CreateFromArray
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
     * @covers \TypeSpecTest\Integration\IntArrayParams
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
            "Value must contain only digits.",
            $errors
        );
    }
}
