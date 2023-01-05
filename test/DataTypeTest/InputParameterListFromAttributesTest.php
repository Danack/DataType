<?php

declare(strict_types = 1);

namespace DataTypeTest;

use ThreeColors;
use DataType\InputType;
use DataType\ExtractRule\GetStringOrDefault;
use DataType\ProcessRule\ImagickIsRgbColor;

/**
 * @coversNothing
 */
class InputParameterListFromAttributesTest extends BaseTestCase
{
    /**
     * @covers \DataType\GetInputTypesFromAttributes
     * @covers \ThreeColors
     */
    function testWorks()
    {
        $inputParameters = ThreeColors::getInputTypes();

        foreach ($inputParameters as $inputParameter) {
            $this->assertInstanceOf(InputType::class, $inputParameter);
            $this->assertInstanceOf(GetStringOrDefault::class, $inputParameter->getExtractRule());

            $processRules = $inputParameter->getProcessRules();
            $this->assertCount(1, $processRules);
            $processRule = $processRules[0];
            $this->assertInstanceOf(ImagickIsRgbColor::class, $processRule);
        }
    }
}
