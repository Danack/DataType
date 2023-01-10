<?php

declare(strict_types=1);

use DataTypeExample\Parameters\SearchParameters;
use VarMap\ArrayVarMap;
use function DataType\generateOpenApiV300DescriptionForDataType;

require __DIR__ . "/../vendor/autoload.php";


$varMap = new ArrayVarMap([
    'search' => "John",
    'limit' => 20,
    'order' => "+date"
]);

$searchParams = SearchParameters::createFromVarMap($varMap);

var_dump($searchParams);

echo "\nExample behaved as expected.\n";


// Example_OpenApi_generation start
$openapi_descriptions = generateOpenApiV300DescriptionForDataType(SearchParameters::class);

echo json_encode($openapi_descriptions, JSON_PRETTY_PRINT);
// Example_OpenApi_generation end

echo "\n";
exit(0);
