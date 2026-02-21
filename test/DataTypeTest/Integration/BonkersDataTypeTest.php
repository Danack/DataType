<?php

namespace DataTypeTest\Integration;

use DataType\Exception\InvalidRulesExceptionData;
use DataType\Messages;
use DataTypeTest\BaseTestCase;
use VarMap\ArrayVarMap;

/**
 * @coversNothing
 */
class BonkersDataTypeTest extends BaseTestCase
{
    /**
     * BonkersDataType has GetDatetime then MaxIntValue - so a DateTime is passed to an int rule and throws.
     */
    public function testCreateFromVarMapThrowsWhenWrongTypePassedToProcessRule(): void
    {
        $varMap = new ArrayVarMap(['bad_type' => '2002-10-02T10:00:00-05:00']);

        $this->expectException(InvalidRulesExceptionData::class);
        $this->expectExceptionMessageMatchesTemplateString(Messages::BAD_TYPE_FOR_INT_PROCESS_RULE);

        BonkersDataType::createFromVarMap($varMap);
    }
}
