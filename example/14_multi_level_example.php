<?php

declare(strict_types=1);


use DataType\Basic\BasicInteger;
use DataType\Basic\BasicString;
use DataType\Create\CreateFromJson;
use VarMap\ArrayVarMap;
use MultiLevelDataType\OuterDataType;

require __DIR__ . "/../vendor/autoload.php";

$data = [
    'repository' => 'php/php-src',
    'base' => [
        'branch' => '8.4',
        'sha' => '7c4a8d2e9f1b6a3c5d8e0f2a4b6c8d1e3f5a7b9c',
    ],
    'head' => [
        'branch' => '8.5',
        'sha' => 'a9f3c7e1b5d2a8f6c4e0b7d9a1c3f5e8b6d4a2c0',
    ],
];






$outer_type = OuterDataType::createFromArray($data);

var_dump($outer_type);

echo "\nExample behaved as expected.\n";


echo "\n";
exit(0);
