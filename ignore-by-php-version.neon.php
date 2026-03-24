<?php declare(strict_types = 1);

$includes = [];

if (PHP_VERSION_ID < 80300) {
    $includes[] = __DIR__ . '/pre-8.3-errors.neon';
}

$config = [];
$config['includes'] = $includes;
$config['parameters']['phpVersion'] = PHP_VERSION_ID;

return $config;