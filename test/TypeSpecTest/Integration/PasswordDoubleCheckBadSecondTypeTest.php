<?php

declare(strict_types = 1);

namespace TypeSpecTest\Integration;

use TypeSpec\Messages;
use VarMap\ArrayVarMap;
use TypeSpecTest\BaseTestCase;

/**
 * @coversNothing
 */
class PasswordDoubleCheckBadSecondTypeTest extends BaseTestCase
{
    /**
     * @covers \TypeSpecTest\Integration\PasswordDoubleCheck
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
