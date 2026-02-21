<?php

declare(strict_types=1);

namespace DataTypeTest\Exception;

use DataType\Exception\LogicExceptionData;
use DataTypeTest\BaseTestCase;

/**
 * @coversNothing
 */
class LogicExceptionTest extends BaseTestCase
{
    /**
     * @covers \DataType\Exception\LogicExceptionData
     */
    public function testWorks()
    {
        $exception = LogicExceptionData::keysMustBeStrings();
        $this->assertStringMatchesTemplateString(
            LogicExceptionData::ONLY_KEYS,
            $exception->getMessage()
        );

        $exception = LogicExceptionData::onlyValidationProblemsAllowed('foo');
        $this->assertStringMatchesTemplateString(
            LogicExceptionData::NOT_VALIDATION_PROBLEM,
            $exception->getMessage()
        );

        $exception = LogicExceptionData::keysMustBeIntegers();
        $this->assertStringMatchesTemplateString(
            LogicExceptionData::ONLY_INT_KEYS,
            $exception->getMessage()
        );

        $exception = LogicExceptionData::missingValue('foo');
        $this->assertStringMatchesTemplateString(
            LogicExceptionData::MISSING_VALUE,
            $exception->getMessage()
        );

        $exception = LogicExceptionData::onlyProcessedValues();
        $this->assertStringMatchesTemplateString(
            LogicExceptionData::ONLY_PROCESSED_VALUES,
            $exception->getMessage()
        );
    }
}
