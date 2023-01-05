<?php

declare(strict_types=1);

namespace DataTypeTest\Exception;

use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\Exception\PropertyHasMultipleInputTypeAnnotationsException;

/**
 * @coversNothing
 */
class PropertyHasMultipleParamAnnotationsExceptionTest extends BaseTestCase
{
    /**
     * This test seems dumb.
     * @covers \DataType\Exception\PropertyHasMultipleInputTypeAnnotationsException
     */
    public function testWorks()
    {
        $exception = PropertyHasMultipleInputTypeAnnotationsException::create(
            'class_name',
            'param_name'
        );

        $this->assertStringMatchesTemplateString(
            Messages::PROPERTY_MULTIPLE_INPUT_TYPE_SPEC,
            $exception->getMessage()
        );
        $this->assertStringContainsString('param_name', $exception->getMessage());
    }
}
