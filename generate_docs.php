<?php

require_once __DIR__ . "/vendor/autoload.php";

function get_classnames($directory, $excluded)
{
    $classnames = [];

    $processRules = glob($directory . "/*.php");

    foreach ($processRules as $processRule) {
        $filename = basename($processRule);
        $classname = str_replace(".php", "", $filename);

        if (in_array($classname, $excluded, true) === true) {
            continue;
        }
        $classnames[] = $classname;
    }

    return $classnames;
}

function get_class_docblocks($classnames, $namespace)
{
    $doc_comments = [];

    foreach ($classnames as $classname) {
        $fqcn = $namespace . '\\' . $classname;

        $rc = new ReflectionClass($fqcn);

        $doc_comment = $rc->getDocComment();

        if ($doc_comment === false) {
            echo "Failed to get doc comment for $classname\n";
            exit(-1);
        }

        $lines = explode("\n", $doc_comment);
        array_shift($lines);
        array_pop($lines);

        $doc_comment_lines = [];
        foreach ($lines as $doc_comment_line) {
            $line = trim($doc_comment_line, " *");
            $doc_comment_lines[] =  trim($line, " *");
        }

        $doc_comments[$classname] = implode("\n", $doc_comment_lines);
    }

    return $doc_comments;
}

$extract_classnames = get_classnames(
    __DIR__ . "/src/TypeSpec/ExtractRule",
    ["ExtractPropertyRule"]
);
$process_classnames = get_classnames(
    __DIR__ . "/src/TypeSpec/ProcessRule",
    ["CheckString"]
);

$doc_comments_for_extract_rules = get_class_docblocks($extract_classnames, 'TypeSpec\ExtractRule');
$doc_comments_for_process_rules = get_class_docblocks($process_classnames, 'TypeSpec\ProcessRule');


$readme = file_get_contents(__DIR__ . "/DOCS_stub_start.md");

$readme .= "## Extract rules \n";

foreach ($doc_comments_for_extract_rules as $name => $comment) {
    $readme .= "\n## $name\n\n";
    $readme .= "$comment \n";
}

$readme .= "\n## Process rules \n";
foreach ($doc_comments_for_process_rules as $name => $comment) {
    $readme .= "## $name \n\n";
    $readme .= "$comment \n";
}

$readme .= file_get_contents(__DIR__ . "/DOCS_stub_end.md");

file_put_contents(__DIR__ . "/DOCS.md", $readme);