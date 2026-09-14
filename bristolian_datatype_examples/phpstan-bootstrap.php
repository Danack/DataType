<?php

declare(strict_types=1);

$localAutoload = __DIR__ . '/vendor/autoload.php';
$parentAutoload = __DIR__ . '/../vendor/autoload.php';

if (is_file($localAutoload)) {
    require $localAutoload;
}
elseif (is_file($parentAutoload)) {
    require $parentAutoload;
}
else {
    fwrite(STDERR, "No composer autoload found. Run composer install in this directory.\n");
    exit(1);
}
