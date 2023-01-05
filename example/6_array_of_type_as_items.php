<?php

declare(strict_types=1);

namespace DataTypeExample;

use DataType\ExtractRule\GetArrayOfType;
use VarMap\ArrayVarMap;
use DataTypeTest\Integration\ReviewScore;
use DataTypeTest\Integration\ItemParams;

require __DIR__ . "/../vendor/autoload.php";



$varMap = new ArrayVarMap([
    'items' => [
        ['score' => 5, 'comment' => 'Hello world'],
        ['score' => 6, 'comment' => 'This is a test.']
    ],
    'description' => 'This is some test data.'
]);

$itemList = ItemParams::createFromVarMap($varMap);

echo "Description: " . $itemList->getDescription() . "\n";

foreach ($itemList->getItems() as $item) {
    echo "Score: " . $item->getScore() . " comment: " . $item->getComment() . "\n";
}

echo "\nExample behaved as expected.\n";
