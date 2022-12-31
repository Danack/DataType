<?php

declare(strict_types=1);

namespace TypeSpecTest;

use TypeSpec\ExtractRule\GetStringOrDefault;
use TypeSpec\DataType;
use TypeSpec\Messages;
use TypeSpec\ProcessRule\ImagickIsRgbColor;
use TypeSpecTest\Integration\IntArrayParams;
use VarMap\ArrayVarMap;
use TypeSpec\Exception\AnnotationClassDoesNotExistException;
use TypeSpec\Exception\IncorrectNumberOfParametersException;
use TypeSpec\Exception\NoConstructorException;
use TypeSpec\Exception\MissingConstructorParameterNameException;
use TypeSpec\Exception\PropertyHasMultipleInputTypeSpecAnnotationsException;
use function TypeSpec\createOrError;

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

        $inputParameters = $threeColors::getDataTypeList();

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

            $this->assertInstanceOf(DataType::class, $inputParameter);

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
