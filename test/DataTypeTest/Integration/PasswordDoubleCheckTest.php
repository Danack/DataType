<?php

declare(strict_types = 1);

namespace DataTypeTest\Integration;

use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

/**
 * @coversNothing
 */
class PasswordDoubleCheckTest extends BaseTestCase
{
    /**
     * @covers \DataTypeTest\Integration\PasswordDoubleCheck
     */
    public function testWorks()
    {
        $password = 'abcde12345';
        $data = [
            'password' => $password,
            'password_repeat' => $password,
        ];

        /** @var PasswordDoubleCheck $duplicateParams */
        [$duplicateParams, $errors] = PasswordDoubleCheck::createOrErrorFromVarMap(
            new ArrayVarMap($data)
        );

        $this->assertValidationErrorCount(0, $errors);

        $this->assertInstanceOf(PasswordDoubleCheck::class, $duplicateParams);
        $this->assertSame($password, $duplicateParams->getPassword());
        $this->assertSame($password, $duplicateParams->getPasswordRepeat());
    }

    public function providesErrors()
    {
        return [
            [

                [
                    'days' => 6,
                    'password_repeat' => 'zyx12345',
                    Messages::ERROR_DIFFERENT_TYPES
                ],

                [
                    'password_repeat' => 'zyx12345',
                    Messages::ERROR_NO_PREVIOUS_PARAMETER
                ],
            ]
        ];
    }

    /**
     * @covers \DataTypeTest\Integration\PasswordDoubleCheck
     */
    public function testDifferentValue()
    {
        $data = [
            'password' => 'abcde12345',
            'password_repeat' => 'zyx12345'
        ];

        /** @var PasswordDoubleCheck $duplicateParams */
        [$duplicateParams, $validationProblems] = PasswordDoubleCheck::createOrErrorFromVarMap(
            new ArrayVarMap($data)
        );

        $this->assertNull($duplicateParams);
        $this->assertCount(1, $validationProblems);

        $this->assertValidationProblem(
            '/password_repeat',
            "Parameter is different to parameter 'password'.",
            $validationProblems
        );
    }

    /**
     * @covers \DataTypeTest\Integration\PasswordDoubleCheck
     */
    public function testMissingPreviousValue()
    {
        $data = [
            'password_repeat' => 'zyx12345'
        ];

        /** @var PasswordDoubleCheck $duplicateParams */
        [$duplicateParams, $validationProblems] = PasswordDoubleCheck::createOrErrorFromVarMap(
            new ArrayVarMap($data)
        );

        $this->assertNull($duplicateParams);
        $this->assertCount(2, $validationProblems);

        $this->assertValidationProblem(
            '/password',
            Messages::VALUE_NOT_SET,
            $validationProblems
        );

        $this->assertValidationProblemRegexp(
            '/password_repeat',
            Messages::ERROR_NO_PREVIOUS_PARAMETER,
            $validationProblems
        );
    }
}
