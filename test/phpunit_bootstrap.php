<?php

use DataType\ExtractRule\GetInt;
use DataType\InputType;
use DataType\ProcessedValue;
use DataType\ProcessedValues;

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/fixtures.php";

function createProcessedValuesFromArray(array $keyValues): ProcessedValues
{
    $processedValues = [];

    foreach ($keyValues as $key => $value) {
        $extractRule = new GetInt();
        $inputParameter = new InputType($key, $extractRule);
        $processedValues[] = new ProcessedValue($inputParameter, $value);
    }

    return ProcessedValues::fromArray($processedValues);
}

function getBoolTestWorks()
{
    yield ['true', true];
    yield ['false', false];

    yield [true, true];
    yield [false, false];
}

function getBoolBadStrings()
{
    yield ['flase'];
    yield ['truuue'];
}
