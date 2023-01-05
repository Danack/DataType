<?php

declare(strict_types=1);

namespace DataTypeTest\Exception;

use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\Exception\MissingClassExceptionData;

/**
 * @coversNothing
 */
class MissingClassExceptionTest extends BaseTestCase
{

    /**
     * @covers \DataType\Exception\MissingClassExceptionData
     */
    public function testInputParameterListException()
    {
        $exception = MissingClassExceptionData::fromClassname(self::class);
        $this->assertStringMatchesTemplateString(
            Messages::CLASS_NOT_FOUND,
            $exception->getMessage()
        );

        $this->assertStringContainsString(self::class, $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
    }
}
