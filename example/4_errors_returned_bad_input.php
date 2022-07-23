<?php

declare(strict_types=1);

use TypeSpecExample\GetArticlesParameters;
use VarMap\ArrayVarMap;

require __DIR__ . "/../vendor/autoload.php";

// Handle errors
$varmap = new ArrayVarMap(['ordering' => 'error']);
[$articleGetIndexParams, $validationErrors] = GetArticlesParameters::createOrErrorFromVarMap($varmap);

if (count($validationErrors) !== 0) {
    echo "There were errors creating ArticleGetIndexParams from input\n  ";
    foreach ($validationErrors as $validationError) {
        /** @var \TypeSpec\ValidationProblem $validationError */
        echo "\n  " . $validationError->getProblemMessage();
    }

    echo "\nExample behaved as expected.\n";
    exit(0);
}

echo "shouldn't reach here.";
exit(-1);
