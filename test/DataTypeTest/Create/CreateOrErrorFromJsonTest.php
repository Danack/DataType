<?php

declare(strict_types = 1);

namespace DataTypeTest\Create;

use DataType\Exception\ValidationException;
use DataTypeTest\BaseTestCase;
use DataTypeTest\Integration\IntArrayParams;
use function DataType\json_encode_safe;

/**
 * @coversNothing
 */
class CreateOrErrorFromJsonTest extends BaseTestCase
{
    /**
     * @covers \DataType\Create\CreateOrErrorFromJson
     */
    public function testWorks()
    {
        $name = 'John';
        $values = [3, 6, 9, 12];
        $data = [
            'name' => $name,
            'counts' => $values
        ];

        $json = json_encode_safe($data);

        [$intArrayParams, $errors] =  IntArrayParams::createOrErrorFromJson($json);

        $this->assertEmpty($errors);
        $this->assertInstanceOf(IntArrayParams::class, $intArrayParams);
        $this->assertSame($name, $intArrayParams->getName());
        $this->assertSame($values, $intArrayParams->getCounts());
    }


    /**
     * @covers \DataType\Create\CreateOrErrorFromJson
     */
    public function testErrors()
    {
        $name = 'John';

        $data = [
            'name' => $name,

        ];

        $json = json_encode_safe($data);

        [$intArrayParams, $errors] =  IntArrayParams::createOrErrorFromJson($json);

        $this->assertNull($intArrayParams);


        $this->assertCount(1, $errors);
        $validationProblem = $errors[0];
        $this->assertInstanceOf(\DataType\ValidationProblem::class, $validationProblem);
        /** @var $validationProblem \DataType\ValidationProblem */

        $this->assertSame(
            \DataType\Messages::VALUE_NOT_SET,
            $validationProblem->getProblemMessage()
        );

        $this->assertSame('/counts', $validationProblem->getInputStorage()->getPath());
    }

    /**
     * @covers \DataType\Create\CreateOrErrorFromJson
     */
    public function testThrowsValidationExceptionWhenJsonRootIsNotAnArray(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('JSON root must be an object (associative array), got string');

        IntArrayParams::createOrErrorFromJson('"hello"');
    }
}
