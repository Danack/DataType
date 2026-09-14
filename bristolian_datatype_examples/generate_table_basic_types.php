<?php

declare(strict_types=1);

/**
 * Builds a markdown table of DataType\Basic\* types from their class files.
 * Descriptions come from each class's docblock (single source of truth).
 *
 * Stub placeholder: <!-- Table_basic_types -->
 * Standalone: php generate_table_basic_types.php
 */

require_once __DIR__ . '/table_generation_helpers.php';

function find_datatype_basic_directory(): string
{
    $candidates = [
        __DIR__ . '/vendor/danack/datatype/src/DataType/Basic',
        __DIR__ . '/../vendor/danack/datatype/src/DataType/Basic',
    ];

    foreach ($candidates as $candidate) {
        if (is_dir($candidate)) {
            return $candidate;
        }
    }

    fwrite(STDERR, "Could not find DataType Basic types directory.\n");
    exit(1);
}

/**
 * @return list<string>
 */
function list_basic_type_class_names(string $basic_directory): array
{
    $php_files = glob($basic_directory . '/*.php');
    if ($php_files === false) {
        fwrite(STDERR, "Failed to list Basic type files in $basic_directory\n");
        exit(1);
    }

    $class_names = [];
    foreach ($php_files as $php_file) {
        $class_names[] = basename($php_file, '.php');
    }

    sort($class_names, SORT_STRING);

    return $class_names;
}

function generate_table_basic_types(): string
{
    require_composer_autoload();

    $basic_directory = find_datatype_basic_directory();
    $class_names = list_basic_type_class_names($basic_directory);

    $table_lines = [
        '| Type | Description |',
        '| --- | --- |',
    ];

    foreach ($class_names as $class_name) {
        $fully_qualified_class_name = 'DataType\\Basic\\' . $class_name;
        $reflection_class = new ReflectionClass($fully_qualified_class_name);

        $doc_comment = $reflection_class->getDocComment();
        if ($doc_comment === false) {
            fwrite(STDERR, "Basic type $class_name has no class docblock.\n");
            exit(1);
        }

        $description = extract_class_docblock_description($doc_comment);
        if ($description === '') {
            fwrite(STDERR, "Basic type $class_name has an empty class description.\n");
            exit(1);
        }

        $table_lines[] = '| `' . $class_name . '` | ' . markdown_table_cell($description) . ' |';
    }

    return implode("\n", $table_lines);
}

$script_name = $_SERVER['argv'][0] ?? '';
if (is_string($script_name) && realpath($script_name) === realpath(__FILE__)) {
    echo generate_table_basic_types() . "\n";
}
