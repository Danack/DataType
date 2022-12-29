<?php

declare(strict_types=1);

namespace TypeSpecExample;

use TypeSpec\OpenApi\OpenApiV300ParamDescription;

require __DIR__ . "/../vendor/autoload.php";

$inputToParamRuleList = GetArticlesParameters::getDataTypeList();

$descriptions = OpenApiV300ParamDescription::createFromInputTypeSpecList($inputToParamRuleList);

echo json_encode($descriptions, JSON_PRETTY_PRINT);


/*
This will output something similar to:

[
    {
        "name": "order",
        "required": false,
        "schema": {
            "default": "-date",
            "type": "array",
            "maxLength": 1024
        }
    },
    {
        "name": "limit",
        "required": false,
        "schema": {
            "minimum": 1,
            "maximum": 200,
            "default": "10",
            "type": "string",
            "exclusiveMaximum": false,
            "exclusiveMinimum": false
        }
    },
    {
        "name": "after",
        "required": false,
        "schema": {
            "minimum": 0,
            "maximum": 1000000000000000,
            "type": "string",
            "exclusiveMaximum": false,
            "exclusiveMinimum": false,
            "nullable": true
        }
    }
]

*/
