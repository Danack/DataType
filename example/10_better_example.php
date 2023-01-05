<?php

declare(strict_types=1);

use TypeSpecExample\Parameters\SearchParameters;
use VarMap\ArrayVarMap;
use function TypeSpec\validate;

require __DIR__ . "/../vendor/autoload.php";


$varMap = new ArrayVarMap([
    'search' => "John",
    'limit' => 20,
    'order' => "+date"
]);


$searchParams = SearchParameters::createFromVarMap($varMap);

var_dump($searchParams);

echo "\nExample behaved as expected.\n";
exit(0);
