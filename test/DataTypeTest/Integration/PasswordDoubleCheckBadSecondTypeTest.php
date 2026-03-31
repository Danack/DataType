<?php

declare(strict_types = 1);

namespace DataTypeTest\Integration;

use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;
use DataTypeTestFixture\Integration\PasswordDoubleCheck;
use DataTypeTestFixture\Integration\PasswordDoubleCheckBadSecondType;

/**
 * @coversNothing
 */
class PasswordDoubleCheckBadSecondTypeTest extends BaseTestCase
{
    /**
     * @covers \DataTypeTestFixture\Integration\PasswordDoubleCheck
     */
    public function testWrongTypePreviousValue()
    {
        $data = [
            'password' => 'zyx12345',
            'password_repeat' => 5
        ];

        /** @var PasswordDoubleCheck $duplicateParams */
        [$duplicateParams, $validationProblems] = PasswordDoubleCheckBadSecondType::createOrErrorFromVarMap(
            new ArrayVarMap($data)
        );

        $this->assertNull($duplicateParams);
        $this->assertCount(1, $validationProblems);

        $this->assertValidationProblemRegexp(
            '/password_repeat',
            Messages::ERROR_DIFFERENT_TYPES,
            $validationProblems
        );
    }
}
