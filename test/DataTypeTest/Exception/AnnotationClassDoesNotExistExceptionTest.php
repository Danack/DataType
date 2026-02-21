<?php

declare(strict_types=1);

namespace DataTypeTest\Exception;

use DataType\Exception\AnnotationClassDoesNotExistExceptionData;
use DataType\Messages;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class AnnotationClassDoesNotExistExceptionTest extends BaseTestCase
{
    /**
     * This test seems dumb.
     * @covers \DataType\Exception\AnnotationClassDoesNotExistExceptionData
     */
    public function testWorks()
    {
        $exception = AnnotationClassDoesNotExistExceptionData::create(
            self::class,
            'property_foo',
            'annotation_bar'
        );

        $expected_message = sprintf(
            Messages::PROPERTY_ANNOTATION_DOES_NOT_EXIST,
            'property_foo',
            self::class,
            'annotation_bar'
        );

        $this->assertSame(
            $expected_message,
            $exception->getMessage()
        );
    }
}
