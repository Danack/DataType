<?php

declare(strict_types=1);

namespace DataTypeTest\Exception;

use DataType\Messages;
use DataTypeTest\BaseTestCase;
use DataType\Exception\DataTypeDefinitionException;

/**
 * @coversNothing
 */
class InputParameterListExceptionTest extends BaseTestCase
{
    /**
     * @covers \DataType\Exception\DataTypeDefinitionException
     */
    public function testInputParameterListException_foundNonInputParameter()
    {
        $position = 3;
        $classname = 'John';

        $exception = DataTypeDefinitionException::foundNonPropertyDefinition($position, $classname);


        $this->assertStringMatchesTemplateString(
            Messages::MUST_RETURN_ARRAY_OF_PROPERTY_DEFINITION,
            $exception->getMessage()
        );
        $this->assertStringContainsString((string)$position, $exception->getMessage());
        $this->assertStringContainsString($classname, $exception->getMessage());
    }
}
