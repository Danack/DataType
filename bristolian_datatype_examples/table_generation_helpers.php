<?php

declare(strict_types=1);

/**
 * Shared helpers for generate_table_*.php scripts.
 */

function require_composer_autoload(): void
{
    $local_autoload = __DIR__ . '/vendor/autoload.php';
    $parent_autoload = __DIR__ . '/../vendor/autoload.php';

    if (is_file($local_autoload)) {
        require_once $local_autoload;
        return;
    }

    if (is_file($parent_autoload)) {
        require_once $parent_autoload;
        return;
    }

    fwrite(STDERR, "No composer autoload found. Run composer install in this directory.\n");
    exit(1);
}

function extract_class_docblock_description(string $doc_comment): string
{
    $lines = preg_split("/\r\n|\n|\r/", $doc_comment);
    if ($lines === false) {
        return '';
    }

    $description_lines = [];
    foreach ($lines as $line) {
        $stripped = preg_replace('/^\s*\/\*\*\s?/', '', $line);
        $stripped = preg_replace('/^\s*\*\//', '', $stripped);
        $stripped = preg_replace('/^\s*\*\s?/', '', $stripped);
        $stripped = trim($stripped);

        if ($stripped === '') {
            if (count($description_lines) > 0) {
                break;
            }
            continue;
        }

        if (str_starts_with($stripped, '@')) {
            break;
        }

        $description_lines[] = $stripped;
    }

    return implode(' ', $description_lines);
}

function markdown_table_cell(string $text): string
{
    $escaped = str_replace('|', '\\|', $text);
    $escaped = str_replace("\n", ' ', $escaped);

    return $escaped;
}
