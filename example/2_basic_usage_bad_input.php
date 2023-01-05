<?php

declare(strict_types=1);

use DataTypeExample\GetArticlesParameters;
use VarMap\ArrayVarMap;
use DataType\Exception\ValidationExceptionData;

require __DIR__ . "/../vendor/autoload.php";

$varMap = new ArrayVarMap([]);

try {
    $varMap = new ArrayVarMap(['ordering' => 'not a valid value']);
    $articleGetIndexParams = GetArticlesParameters::createFromVarMap($varMap);

    echo "shouldn't reach here.";
    exit(-1);
}
catch (ValidationExceptionData $ve) {
    echo "There were validation problems parsing the input:\n  ";
    echo implode("\n  ", $ve->getValidationProblemsAsStrings());

    echo "\nExample behaved as expected.\n";
    exit(0);
}
