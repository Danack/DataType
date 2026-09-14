<?php

declare(strict_types=1);

namespace DataTypeTest\Exception;

use DataType\Exception\Logic\KeysMustBeIntegersException;
use DataType\Exception\Logic\KeysMustBeStringsException;
use DataType\Exception\Logic\MissingValueException;
use DataType\Exception\Logic\OnlyProcessedValuesException;
use DataType\Exception\Logic\OnlyValidationProblemsAllowedException;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class LogicExceptionTest extends BaseTestCase
{
    /**
     * @covers \DataType\Exception\Logic\KeysMustBeStringsException
     * @covers \DataType\Exception\Logic\OnlyValidationProblemsAllowedException
     * @covers \DataType\Exception\Logic\KeysMustBeIntegersException
     * @covers \DataType\Exception\Logic\MissingValueException
     * @covers \DataType\Exception\Logic\OnlyProcessedValuesException
     */
    public function testWorks()
    {
        $exception = new KeysMustBeStringsException();
        $this->assertStringMatchesTemplateString(
            KeysMustBeStringsException::MESSAGE,
            $exception->getMessage()
        );

        $exception = new OnlyValidationProblemsAllowedException('foo');
        $this->assertStringMatchesTemplateString(
            OnlyValidationProblemsAllowedException::MESSAGE,
            $exception->getMessage()
        );

        $exception = new KeysMustBeIntegersException();
        $this->assertStringMatchesTemplateString(
            KeysMustBeIntegersException::MESSAGE,
            $exception->getMessage()
        );

        $exception = new MissingValueException('foo');
        $this->assertStringMatchesTemplateString(
            MissingValueException::MESSAGE,
            $exception->getMessage()
        );

        $exception = new OnlyProcessedValuesException();
        $this->assertStringMatchesTemplateString(
            OnlyProcessedValuesException::MESSAGE,
            $exception->getMessage()
        );
    }
}
