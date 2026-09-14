<?php

declare(strict_types=1);

/**
 * Builds a markdown hierarchy of DataType\Exception\* classes from their files.
 * Parent class comes from reflection; descriptions from class docblocks when present.
 *
 * Stub placeholder: <!-- Table_exceptions -->
 * Standalone: php generate_table_exceptions.php
 */

require_once __DIR__ . '/table_generation_helpers.php';

function find_datatype_exception_directory(): string
{
    $candidates = [
        __DIR__ . '/vendor/danack/datatype/src/DataType/Exception',
        __DIR__ . '/../vendor/danack/datatype/src/DataType/Exception',
        __DIR__ . '/../src/DataType/Exception',
    ];

    foreach ($candidates as $candidate) {
        if (is_dir($candidate)) {
            return $candidate;
        }
    }

    fwrite(STDERR, "Could not find DataType Exception directory.\n");
    exit(1);
}

/**
 * @return list<string> Fully-qualified class names
 */
function list_exception_fully_qualified_class_names(string $exception_directory): array
{
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($exception_directory, FilesystemIterator::SKIP_DOTS)
    );

    $class_names = [];
    $prefix_length = strlen(rtrim($exception_directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);

    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        $relative_path = substr($file->getPathname(), $prefix_length);
        $relative_without_extension = substr($relative_path, 0, -strlen('.php'));
        $relative_namespace_path = str_replace(DIRECTORY_SEPARATOR, '\\', $relative_without_extension);
        $class_names[] = 'DataType\\Exception\\' . $relative_namespace_path;
    }

    sort($class_names, SORT_STRING);

    return $class_names;
}

function format_exception_list_item(ReflectionClass $reflection_class): string
{
    $item = '`' . $reflection_class->getShortName() . '`';
    $doc_comment = $reflection_class->getDocComment();
    if ($doc_comment === false) {
        return $item;
    }

    $detail_parts = [];
    $description = extract_class_docblock_description($doc_comment);
    if ($description !== '') {
        $detail_parts[] = $description;
    }

    if (str_contains($doc_comment, '@unchecked')) {
        $detail_parts[] = 'Unchecked.';
    }
    elseif (str_contains($doc_comment, '@checked')) {
        $detail_parts[] = 'Checked.';
    }

    if (count($detail_parts) === 0) {
        return $item;
    }

    return $item . ' — ' . implode(' ', $detail_parts);
}

/**
 * @param array<string, ReflectionClass> $reflections_by_class_name
 * @param array<string, list<string>> $children_by_parent
 * @return list<string>
 */
function render_exception_hierarchy_lines(
    string $class_name,
    array $reflections_by_class_name,
    array $children_by_parent,
    int $depth
): array {
    $indent = str_repeat('  ', $depth);
    $lines = [
        $indent . '- ' . format_exception_list_item($reflections_by_class_name[$class_name]),
    ];

    $child_class_names = $children_by_parent[$class_name] ?? [];
    sort($child_class_names, SORT_STRING);

    foreach ($child_class_names as $child_class_name) {
        $child_lines = render_exception_hierarchy_lines(
            $child_class_name,
            $reflections_by_class_name,
            $children_by_parent,
            $depth + 1
        );
        foreach ($child_lines as $child_line) {
            $lines[] = $child_line;
        }
    }

    return $lines;
}

function generate_table_exceptions(): string
{
    require_composer_autoload();

    $exception_directory = find_datatype_exception_directory();
    $class_names = list_exception_fully_qualified_class_names($exception_directory);

    /** @var array<string, ReflectionClass> $reflections_by_class_name */
    $reflections_by_class_name = [];
    foreach ($class_names as $fully_qualified_class_name) {
        $reflection_class = new ReflectionClass($fully_qualified_class_name);
        $reflections_by_class_name[$fully_qualified_class_name] = $reflection_class;
    }

    /** @var array<string, list<string>> $children_by_parent */
    $children_by_parent = [];
    $root_class_names = [];

    foreach ($reflections_by_class_name as $fully_qualified_class_name => $reflection_class) {
        $parent_class = $reflection_class->getParentClass();
        if ($parent_class === false) {
            $root_class_names[] = $fully_qualified_class_name;
            continue;
        }

        $parent_class_name = $parent_class->getName();
        if (array_key_exists($parent_class_name, $reflections_by_class_name)) {
            $children_by_parent[$parent_class_name][] = $fully_qualified_class_name;
        }
        else {
            $root_class_names[] = $fully_qualified_class_name;
        }
    }

    sort($root_class_names, SORT_STRING);

    $lines = [];
    foreach ($root_class_names as $root_class_name) {
        $root_lines = render_exception_hierarchy_lines(
            $root_class_name,
            $reflections_by_class_name,
            $children_by_parent,
            0
        );
        foreach ($root_lines as $root_line) {
            $lines[] = $root_line;
        }
    }

    return implode("\n", $lines);
}

$script_name = $_SERVER['argv'][0] ?? '';
if (is_string($script_name) && realpath($script_name) === realpath(__FILE__)) {
    echo generate_table_exceptions() . "\n";
}
