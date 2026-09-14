<?php

declare(strict_types=1);

/**
 * Standard usage: CreateOrError* instead of throwing ValidationException.
 * Doc extract: Example_standard_create_or_error
 */

namespace BristolianDatatypeExamples\Standard;

use DataType\Basic\BasicString;
use DataType\Create\CreateOrErrorFromArray;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataType\ValidationProblem;

// Example_standard_create_or_error start
class RenameParams implements DataType
{
    use CreateOrErrorFromArray;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[BasicString('name')]
        public readonly string $name,
    ) {
    }
}

[$params, $validationProblems] = RenameParams::createOrErrorFromArray([
    // 'name' missing → validation failure
]);

if (count($validationProblems) > 0) {
    foreach ($validationProblems as $problem) {
        /** @var ValidationProblem $problem */
        // $problem->getProblemMessage(), path via data storage
    }
    return;
}

// $params is RenameParams
// Example_standard_create_or_error end
