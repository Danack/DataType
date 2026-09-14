<?php

declare(strict_types=1);

/**
 * Generates README.md from README_stub.md + example extracts + Basic types table.
 * Same marker convention as vendor/danack/datatype/generate_docs.php:
 *   // Example_name start
 *   ...code...
 *   // Example_name end
 * Placeholder in stub: <!-- Example_name -->
 * Tables: generate_table_{name}.php fills <!-- Table_{name} -->
 */

$stubPath = __DIR__ . '/README_stub.md';
$examplesDir = __DIR__ . '/examples/documentation';
$outputPath = __DIR__ . '/README.md';

function find_example_in_file(string $example_name, string $example_filename): array|null
{
    $example_start = "// $example_name start";
    $example_end = "// $example_name end";

    $file_lines = file($example_filename);
    if ($file_lines === false) {
        return null;
    }

    $example_lines = [];
    $example_started = false;
    foreach ($file_lines as $file_line) {
        if ($example_started === true) {
            if (strpos($file_line, $example_end) === 0) {
                return $example_lines;
            }
            $example_lines[] = $file_line;
        }
        if (strpos($file_line, $example_start) === 0) {
            $example_started = true;
        }
    }

    if ($example_started === true) {
        fwrite(STDERR, "Example $example_name started, but not ended in $example_filename\n");
        exit(1);
    }

    return null;
}

function getExampleCode(string $example_name, string $examplesDir): string
{
    $example_files = glob($examplesDir . '/*.php');
    if ($example_files === false) {
        fwrite(STDERR, "No example files in $examplesDir\n");
        exit(1);
    }

    foreach ($example_files as $example_file) {
        if (preg_match('/\.\d+\.php$/', $example_file) === 1) {
            continue;
        }

        $lines = find_example_in_file($example_name, $example_file);
        if ($lines !== null) {
            return implode('', $lines);
        }
    }

    fwrite(STDERR, "Failed to find example '$example_name'\n");
    exit(1);
}

$readme = file_get_contents($stubPath);
if ($readme === false) {
    fwrite(STDERR, "Failed to read $stubPath\n");
    exit(1);
}

$table_scripts = glob(__DIR__ . '/generate_table_*.php');
if ($table_scripts === false) {
    fwrite(STDERR, "Failed to list generate_table_*.php scripts.\n");
    exit(1);
}

foreach ($table_scripts as $table_script) {
    $script_basename = basename($table_script);
    if (preg_match('/^generate_table_(.+)\.php$/', $script_basename, $matches) !== 1) {
        continue;
    }

    $table_name = $matches[1];
    $table_placeholder = '<!-- Table_' . $table_name . ' -->';
    if (str_contains($readme, $table_placeholder) !== true) {
        fwrite(STDERR, "Failed to find '$table_placeholder' in stub (from $script_basename).\n");
        exit(1);
    }

    require_once $table_script;

    $table_function_name = 'generate_table_' . $table_name;
    if (function_exists($table_function_name) !== true) {
        fwrite(STDERR, "$script_basename must define $table_function_name().\n");
        exit(1);
    }

    $readme = str_replace(
        $table_placeholder,
        $table_function_name(),
        $readme
    );
}

$example_list = [
    'Example_search_controller_usage',
    'Example_search_datatype',
    'Example_aesthetic_create_exception',
    'Example_aesthetic_create_or_error',
    'Example_using_static_factory_pattern',
    'Example_configuring_static_factory_injector',
    'Example_static_factory_boring_details',
    'Example_datatype_without_attributes',
    'Example_laravel_controller',
    'Example_laravel_form_request_adapter',
];

foreach ($example_list as $example) {
    $example_to_replace = '<!-- ' . $example . ' -->';

    if (str_contains($readme, $example_to_replace) !== true) {
        fwrite(STDERR, "Failed to find '$example_to_replace' in stub.\n");
        exit(1);
    }

    $readme = str_replace(
        $example_to_replace,
        getExampleCode($example, $examplesDir),
        $readme
    );
}

file_put_contents($outputPath, $readme);
echo "Wrote $outputPath\n";
