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

    $comment_starts = [
        "/**",
        " * ",
        " *",
        " */",
    ];


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
            foreach ($comment_starts as $comment_start) {
                if (str_starts_with($doc_comment_line, $comment_start) === true) {
                    $doc_comment_line = substr($doc_comment_line, strlen($comment_start));
                }
            }
            $line = trim($doc_comment_line);

            if (strlen($line) >= 0) {
                $doc_comment_lines[] = $line;
            }
        }

        $doc_comments[$classname] = implode("\n", $doc_comment_lines);
    }

    return $doc_comments;
}

$extract_classnames = get_classnames(
    __DIR__ . "/src/DataType/ExtractRule",
    ["ExtractRule"]
);

$doc_comments_for_extract_rules = get_class_docblocks(
    $extract_classnames,
    'DataType\ExtractRule'
);

$process_classnames = get_classnames(
    __DIR__ . "/src/DataType/ProcessRule",
    ["ProcessRule", "CheckString"]
);
$doc_comments_for_process_rules = get_class_docblocks(
    $process_classnames,
    'DataType\ProcessRule'
);



$readme = file_get_contents(__DIR__ . "/DOCS_stub_start.md");

$readme .= "## Extract rules\n";

$readme .= <<< EXTRACT_MD

The extract rules determine how values are extracted from the source data. As the source data is often composed of just strings, they can convert the value to a int, float, boolean or other type. 

EXTRACT_MD;

$readme .= "\n";
$readme .= "| Type        | Description |\n";
$readme .= "| :---------  | :---------- |\n";
foreach ($doc_comments_for_extract_rules as $name => $comment) {
    $comment = str_replace("\n", "<br/>", $comment);
    $readme .= "| $name | $comment |\n";
}

$readme .= <<< PROCESS_MD


PROCESS_MD;

$readme .= "\n## Process rules \n";


$readme .= "\n";
$readme .= "| Type        | Description |\n";
$readme .= "| :---------  | :---------- |\n";

foreach ($doc_comments_for_process_rules as $name => $comment) {
    $comment = str_replace("\n", "<br/>", $comment);
    $readme .= "| $name | $comment |\n";
}


$create_classnames = get_classnames(
    __DIR__ . "/src/DataType/Create",
    ["CreateArrayOfTypeFromArray"]
);
$doc_comments_for_create_traits = get_class_docblocks(
    $create_classnames,
    'DataType\Create'
);


$readme .= "\n## Create traits\n";

$readme .= <<< TRAIT_MD

The library includes several traits to make DataTypes easier to use. The traits fall into two categories, those that return errors, and those that throw exceptions if there is an error.

* CreateFromX will create the DataType or throw a ValidationException if there is a problem with the input data.

* CreateOrErrorFromX will return two values, the created DataType and an empty array if there were no validation problems, or null and an array of ValidationProblems if there were problems.

TRAIT_MD;

$readme .= "\n";
$readme .= "| Type        | Description |\n";
$readme .= "| :---------  | :---------- |\n";

foreach ($doc_comments_for_create_traits as $name => $comment) {
    $comment = str_replace("\n", "<br/>", $comment);
    $readme .= "| $name | $comment |\n";
}

$readme .= file_get_contents(__DIR__ . "/DOCS_stub_end.md");

$example_list = [
    'Example_basic_usage',
    'Example_without_annotations',
    'Example_OpenApi_generation',
];


foreach ($example_list as $example) {

    $example_to_replace = "<!-- " . $example . " -->";

    if (str_contains($readme, $example_to_replace) !== true) {
        echo "Failed to find '$example_to_replace' in generated doc.\n";
        exit(-1);
    }

    $readme = str_replace(
        $example_to_replace,
        "Example code goes here.",
        $readme
    );
}


file_put_contents(__DIR__ . "/DOCS.md", $readme);