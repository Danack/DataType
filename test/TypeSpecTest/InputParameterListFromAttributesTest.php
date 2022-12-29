<?php

declare(strict_types = 1);

namespace TypeSpecTest;

use ThreeColors;
use TypeSpec\DataType;
use TypeSpec\ExtractRule\GetStringOrDefault;
use TypeSpec\ProcessRule\ImagickIsRgbColor;

/**
 * @coversNothing
 */
class InputParameterListFromAttributesTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\GetDataTypeListFromAttributes
     * @covers \ThreeColors
     */
    function testWorks()
    {
        $inputParameters = ThreeColors::getDataTypeList();

        foreach ($inputParameters as $inputParameter) {
            $this->assertInstanceOf(DataType::class, $inputParameter);
            $this->assertInstanceOf(GetStringOrDefault::class, $inputParameter->getExtractRule());

            $processRules = $inputParameter->getProcessRules();
            $this->assertCount(1, $processRules);
            $processRule = $processRules[0];
            $this->assertInstanceOf(ImagickIsRgbColor::class, $processRule);
        }
    }
}
