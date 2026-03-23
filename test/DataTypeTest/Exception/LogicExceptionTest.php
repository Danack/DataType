<?php

declare(strict_types=1);

namespace DataTypeTest\Exception;

use DataType\Exception\DataTypeLogicException;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class LogicExceptionTest extends BaseTestCase
{
    /**
     * @covers \DataType\Exception\DataTypeLogicException
     */
    public function testWorks()
    {
        $exception = DataTypeLogicException::keysMustBeStrings();
        $this->assertStringMatchesTemplateString(
            DataTypeLogicException::ONLY_KEYS,
            $exception->getMessage()
        );

        $exception = DataTypeLogicException::onlyValidationProblemsAllowed('foo');
        $this->assertStringMatchesTemplateString(
            DataTypeLogicException::NOT_VALIDATION_PROBLEM,
            $exception->getMessage()
        );

        $exception = DataTypeLogicException::keysMustBeIntegers();
        $this->assertStringMatchesTemplateString(
            DataTypeLogicException::ONLY_INT_KEYS,
            $exception->getMessage()
        );

        $exception = DataTypeLogicException::missingValue('foo');
        $this->assertStringMatchesTemplateString(
            DataTypeLogicException::MISSING_VALUE,
            $exception->getMessage()
        );

        $exception = DataTypeLogicException::onlyProcessedValues();
        $this->assertStringMatchesTemplateString(
            DataTypeLogicException::ONLY_PROCESSED_VALUES,
            $exception->getMessage()
        );
    }
}
