<?php

declare(strict_types=1);

require dirname(__DIR__) . '/phpstan-bootstrap.php';

$runnableDirectory = dirname(__DIR__) . '/examples/runnable';
$runnableFiles = glob($runnableDirectory . '/*.php');
if ($runnableFiles === false) {
    fwrite(STDERR, "No runnable example files found.\n");
    exit(1);
}

foreach ($runnableFiles as $runnableFile) {
    require_once $runnableFile;
}
