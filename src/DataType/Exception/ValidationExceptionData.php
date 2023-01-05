<?php

declare(strict_types = 1);

namespace DataType\Exception;

class ValidationExceptionData extends DataTypeException
{
    /**
     * @var \DataType\ValidationProblem[]
     */
    private array $validationProblems;

    /**
     * ValidationException constructor.
     * @param string $message
     * @param \DataType\ValidationProblem[] $validationProblems
     * @param \Exception|null $previous
     */
    public function __construct(string $message, array $validationProblems, \Exception $previous = null)
    {
        $actualMessage = $message . " ";

        $problemStrings = [];
        foreach ($validationProblems as $validationProblem) {
            $problemStrings[] = $validationProblem->toString();
        }

        $actualMessage .= implode(", ", $problemStrings);

        $this->validationProblems = $validationProblems;

        parent::__construct($actualMessage, $code = 0, $previous);
    }

    /**
     * @return \DataType\ValidationProblem[]
     */
    public function getValidationProblems(): array
    {
        return $this->validationProblems;
    }

    /**
     * @return string[]
     */
    public function getValidationProblemsAsStrings(): array
    {
        $strings = [];
        foreach ($this->validationProblems as $validationProblem) {
            $strings[] = $validationProblem->toString();
        }

        return $strings;
    }
}
