<?php

declare(strict_types=1);

namespace DataTypeTest;

use DataType\ExtractRule\GetStringOrDefault;
use DataType\InputType;
use DataType\Messages;
use DataType\ProcessRule\ImagickIsRgbColor;
use DataTypeTest\Integration\IntArrayParams;
use VarMap\ArrayVarMap;
use DataType\Exception\AnnotationClassDoesNotExistExceptionData;
use DataType\Exception\IncorrectNumberOfParametersExceptionData;
use DataType\Exception\NoConstructorExceptionData;
use DataType\Exception\MissingConstructorParameterNameExceptionData;
use DataType\Exception\PropertyHasMultipleInputTypeAnnotationsException;
use function DataType\createOrError;

/**
 * @coversNothing
 * TODO - this class duplicates some tests that should be elsewhere.
 */
class ParamAnnotationsTest extends BaseTestCase
{
    public function testCreateFromVarMapWorks()
    {
        $varMap = new ArrayVarMap([
            'background_color' => 'red',
            'stroke_color' => 'rgb(255, 0, 255)',
            'fill_color' => 'white',
        ]);

        $threeColors = \ThreeColors::createFromVarMap($varMap);
        $this->assertInstanceOf(\ThreeColors::class, $threeColors);
    }

    public function testGetParamsFromAnnotation()
    {
        $varMap = new ArrayVarMap([
            'background_color' => 'red',
            'stroke_color' => 'rgb(255, 0, 255)',
            'fill_color' => 'white',
        ]);

        $threeColors = \ThreeColors::createFromVarMap($varMap);
        $this->assertInstanceOf(\ThreeColors::class, $threeColors);

        $inputParameters = $threeColors::getInputTypes();

        $this->assertCount(3, $inputParameters);

        $namesAndDefaults = [
            ['background_color', 'rgb(225, 225, 225)'],
            ['stroke_color', 'rgb(0, 0, 0)'],
            ['fill_color', 'DodgerBlue2'],
        ];

        $count = 0;
        foreach ($inputParameters as $inputParameter) {
            $expectedName = $namesAndDefaults[$count][0];
            $expectedDefault = $namesAndDefaults[$count][1];

            $this->assertInstanceOf(InputType::class, $inputParameter);

            $this->assertSame(
                $expectedName,
                $inputParameter->getName()
            );

            $extractRule = $inputParameter->getExtractRule();
            $this->assertInstanceOf(
                GetStringOrDefault::class,
                $extractRule
            );

            /** @var GetStringOrDefault $extractRule */
            $this->assertSame(
                $expectedDefault,
                $extractRule->getDefault()
            );

            $count += 1;
        }
    }
}
