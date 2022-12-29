<?php

declare(strict_types = 1);

namespace TypeSpec;

use TypeSpec\ExtractRule\ExtractPropertyRule;
use TypeSpec\ProcessRule\ProcessPropertyRule;

/**
 * The definition of how a type should be extracted
 * and processed.
 */
class DataType
{
    /**
     * The name of the input to use.
     */
    private string $name;

    private ?string $target_parameter_name = null;

    /**
     * The rule to extract the parameter from the input.
     */
    private ExtractPropertyRule $extractRule;

    /**
     * The subsequent rules to process the parameter.
     * @var \TypeSpec\ProcessRule\ProcessPropertyRule[]
     */
    private array $processRules;

    /**
     *
     * @param string $input_name The key/name that the initial value will be extracted from
     * @param ExtractPropertyRule $extract_rule The initial rule that will extract the value from the source data where it will exist as a string.
     * @param ProcessPropertyRule ...$subsequent_rules The subsequent rules that will be used to process the value.
     */
    public function __construct(
        string              $input_name, // TODO - this should be a locator component...
        ExtractPropertyRule $extract_rule,
        ProcessPropertyRule ...$subsequent_rules
    ) {
        $this->name = $input_name;
        $this->extractRule = $extract_rule;
        $this->processRules = $subsequent_rules;
    }

    public function setTargetParameterName(string $name): void
    {
        $this->target_parameter_name = $name;
    }

    /**
     * @return string
     */
    public function getTargetParameterName(): string
    {
        if ($this->target_parameter_name === null) {
            return $this->name;
        }

        return $this->target_parameter_name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return ExtractPropertyRule
     */
    public function getExtractRule(): ExtractPropertyRule
    {
        return $this->extractRule;
    }

    /**
     * @return ProcessPropertyRule[]
     */
    public function getProcessRules(): array
    {
        return $this->processRules;
    }
}
