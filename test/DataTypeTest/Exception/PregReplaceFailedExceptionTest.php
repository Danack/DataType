<?php

declare(strict_types=1);

namespace DataTypeTest\Exception;

use DataType\Exception\Logic\PregReplaceFailedException;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class PregReplaceFailedExceptionTest extends BaseTestCase
{
    /**
     * @covers \DataType\Exception\Logic\PregReplaceFailedException
     */
    public function test_forPattern_includes_pattern_in_message(): void
    {
        $exception = PregReplaceFailedException::forPattern('/foo/');

        $this->assertSame(
            'preg_replace failed for pattern: /foo/',
            $exception->getMessage()
        );
    }
}
