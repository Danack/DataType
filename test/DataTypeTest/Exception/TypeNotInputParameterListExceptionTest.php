<?php

declare(strict_types=1);

namespace DataTypeTest\Exception;

use DataType\DataType;
use DataType\Exception\DataTypeNotImplementedException;
use DataType\Messages;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class TypeNotInputParameterListExceptionTest extends BaseTestCase
{
    /**
     * @covers \DataType\Exception\DataTypeNotImplementedException
     */
    public function testInputParameterListException()
    {
        $exception = DataTypeNotImplementedException::fromClassname(self::class);
        $this->assertStringMatchesTemplateString(
            Messages::CLASS_MUST_IMPLEMENT_DATATYPE_INTERFACE,
            $exception->getMessage()
        );

        // This should survive class renaming.
        $this->assertStringContainsString(DataType::class, $exception->getMessage());
        $this->assertStringContainsString(self::class, $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
    }
}
