<?php

declare(strict_types=1);

namespace DataTypeTest\Exception;

use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\Exception\NoConstructorExceptionData;

/**
 * @coversNothing
 */
class NoConstructorExceptionTest extends BaseTestCase
{
    /**
     * This test seems dumb.
     * @covers \DataType\Exception\NoConstructorExceptionData
     */
    public function testNoConstructorWorks()
    {
        $exception = NoConstructorExceptionData::noConstructor(
            'John'
        );

        $this->assertStringMatchesTemplateString(
            Messages::CLASS_LACKS_CONSTRUCTOR,
            $exception->getMessage()
        );
        $this->assertStringContainsString('John', $exception->getMessage());
    }

    /**
     * This test seems dumb.
     * @covers \DataType\Exception\NoConstructorExceptionData
     */
    public function testnotPublicConstructor()
    {
        $exception = NoConstructorExceptionData::notPublicConstructor(
            'John'
        );

        $this->assertStringMatchesTemplateString(
            Messages::CLASS_LACKS_PUBLIC_CONSTRUCTOR,
            $exception->getMessage()
        );
        $this->assertStringContainsString('John', $exception->getMessage());
    }
}
