<?php

declare(strict_types=1);

namespace DataTypeExample;

require __DIR__ . "/../vendor/autoload.php";



$correctData = [
    'name' => 'Dan',
    'macAddress' => 'a1:b2:c3:d4:e5:f6'
];

/** @var ComputerDetails $computerDetails */
[$computerDetails, $validationErrors] =
    ComputerDetails::createOrErrorFromArray($correctData);

if (count($validationErrors) !== 0) {
    echo "Unexpected problems.";
    var_dump($validationErrors);
    exit(-1);
}

printf(
    "Correct data\n\tName: [%s]\tMac address [%s]\n",
    $computerDetails->getName(),
    $computerDetails->getMacAddress()
);

$badData = [
    'name' => 'Dan',
    'mac_address' => 'a1:b2:c3:d4:e5:banana'
];

/** @var \DataType\ValidationProblem[] $validationErrors */
[$computerDetails, $validationErrors] =
    ComputerDetails::createOrErrorFromArray($badData);

if (count($validationErrors) === 0) {
    echo "";
    echo "shouldn't reach here.";
    exit(-1);
}

echo "Bad data correctly detected: \n";

foreach ($validationErrors as $validationError) {
    /** @var \DataType\ValidationProblem $validationError */
    echo "\t" . $validationError->getProblemMessage() . "\n";
}


echo "\nExample behaved as expected.\n";
