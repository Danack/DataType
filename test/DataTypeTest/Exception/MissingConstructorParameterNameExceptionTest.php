<?php

declare(strict_types=1);

namespace DataTypeTest\Exception;

use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\Exception\MissingConstructorParameterNameExceptionData;

/**
 * @coversNothing
 */
class MissingConstructorParameterNameExceptionTest extends BaseTestCase
{
    /**
     * This test seems dumb.
     * @covers \DataType\Exception\MissingConstructorParameterNameExceptionData
     */
    public function testWorks()
    {
        $exception = MissingConstructorParameterNameExceptionData::missingParam(
            'class_name',
            'param_name'
        );

        $this->assertStringMatchesTemplateString(
            Messages::MISSING_PARAMETER_NAME,
            $exception->getMessage()
        );
        $this->assertStringContainsString('param_name', $exception->getMessage());
    }
}
