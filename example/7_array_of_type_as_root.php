<?php

declare(strict_types=1);

namespace DataTypeExample;

use DataType\ExtractRule\GetArrayOfType;
use VarMap\ArrayVarMap;
use DataTypeTest\Integration\ReviewScore;
use DataTypeTest\Integration\ItemParams;

require __DIR__ . "/../vendor/autoload.php";


$data = [
    ['score' => 5, 'comment' => 'Hello world'],
    ['score' => 6, 'comment' => 'Hello world2']
];

$items = ReviewScore::createArrayOfTypeFromArray($data);

foreach ($items as $item) {
    echo "Score: " . $item->getScore() . " comment: " . $item->getComment() . "\n";
}


echo "\nExample behaved as expected.\n";
