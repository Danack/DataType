<?php

declare(strict_types=1);

use DataType\Create\DataTypeFactory;
use DataType\Create\DataTypeFactoryOrError;
use DataType\ExtractRule\GetIntOrDefault;
use DataType\ExtractRule\GetString;
use DataType\InputType;
use DataType\ProcessRule\MaxIntValue;
use DataType\ProcessRule\MaxLength;
use DataType\ProcessRule\MinIntValue;
use DataType\ProcessRule\MinLength;
use DataType\DataType;

require __DIR__ . "/../vendor/autoload.php";

// Example using DataTypeFactory (exception-throwing pattern)
class SearchParamsWithExceptions implements DataType
{
    use DataTypeFactory;

    public function __construct(
        public string $query,
        public int $limit,
    ) {
    }

    /**
     * @return InputType[]
     */
    public static function getInputTypes(): array
    {
        return [
            new InputType(
                'query',
                new GetString(),
                new MinLength(3),
                new MaxLength(200),
            ),
            new InputType(
                'limit',
                new GetIntOrDefault(20),
                new MinIntValue(1),
                new MaxIntValue(200),
            ),
        ];
    }
}

// Example using DataTypeFactoryOrError (error-returning pattern)
class SearchParamsWithErrors implements DataType
{
    use DataTypeFactoryOrError;

    public function __construct(
        public string $query,
        public int $limit,
    ) {
    }

    /**
     * @return InputType[]
     */
    public static function getInputTypes(): array
    {
        return [
            new InputType(
                'query',
                new GetString(),
                new MinLength(3),
                new MaxLength(200),
            ),
            new InputType(
                'limit',
                new GetIntOrDefault(20),
                new MinIntValue(1),
                new MaxIntValue(200),
            ),
        ];
    }
}

echo "=== Example 1: Using DataTypeFactory (exception-throwing) ===\n\n";

// Using exception-throwing pattern
try {
    $data = [
        'query' => 'PHP',
        'limit' => 10,
    ];
    
    $params = SearchParamsWithExceptions::createFromArray($data);
    echo "Query: {$params->query}\n";
    echo "Limit: {$params->limit}\n";
    echo "Success!\n\n";
} catch (\DataType\Exception\ValidationException $e) {
    echo "Validation failed:\n";
    foreach ($e->getValidationProblemsAsStrings() as $error) {
        echo "  - $error\n";
    }
}

echo "\n=== Example 2: Using DataTypeFactoryOrError (error-returning) ===\n\n";

// Using error-returning pattern
$data = [
    'query' => 'PHP',
    'limit' => 10,
];

[$params, $errors] = SearchParamsWithErrors::createOrErrorFromArray($data);

if (count($errors) > 0) {
    echo "Validation failed:\n";
    foreach ($errors as $error) {
        echo "  - {$error->getProblemMessage()}\n";
    }
} else {
    echo "Query: {$params->query}\n";
    echo "Limit: {$params->limit}\n";
    echo "Success!\n";
}

echo "\n=== Example 3: Demonstrating all factory methods ===\n\n";

// All methods are available from a single trait
$json = '{"query": "test", "limit": 5}';
$params = SearchParamsWithExceptions::createFromJson($json);
echo "Created from JSON: query={$params->query}, limit={$params->limit}\n";

$varMap = new \VarMap\ArrayVarMap(['query' => 'test2', 'limit' => 15]);
$params = SearchParamsWithExceptions::createFromVarMap($varMap);
echo "Created from VarMap: query={$params->query}, limit={$params->limit}\n";

echo "\nExample behaved as expected.\n";
exit(0);


