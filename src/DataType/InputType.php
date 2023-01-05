<?php

declare(strict_types = 1);

namespace DataType;

use DataType\ExtractRule\ExtractRule;
use DataType\ProcessRule\ProcessRule;

/**
 * The definition of how a type should be extracted from a particular
 * input name, including the extraction rule and processing rules.
 */
class InputType
{
    /**
     * The name of the input to use.
     */
    private string $name;

    /**
     * The name of the parameter that the type will bind to. This only needs
     * to be set if the api name is different from the property name.
     * @var string|null
     */
    private ?string $target_parameter_name = null;

    /**
     * The rule to extract the parameter from the input.
     */
    private ExtractRule $extractRule;

    /**
     * The subsequent rules to process the parameter.
     * @var \DataType\ProcessRule\ProcessRule[]
     */
    private array $processRules;

    /**
     *
     * @param string $input_name The key/name that the initial value will be extracted from
     * @param ExtractRule $extract_rule The initial rule that will extract the value from the source data where it will exist as a string.
     * @param ProcessRule ...$subsequent_rules The subsequent rules that will be used to process the value.
     */
    public function __construct(
        string              $input_name, // TODO - this should be a locator component...
        ExtractRule         $extract_rule,
        ProcessRule ...$subsequent_rules
    ) {
        $this->name = $input_name;
        $this->extractRule = $extract_rule;
        $this->processRules = $subsequent_rules;
    }

    /**
     * @param string $name
     * @return void
     */
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
     * @return ExtractRule
     */
    public function getExtractRule(): ExtractRule
    {
        return $this->extractRule;
    }

    /**
     * @return ProcessRule[]
     */
    public function getProcessRules(): array
    {
        return $this->processRules;
    }
}
