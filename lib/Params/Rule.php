<?php

declare(strict_types=1);

namespace Params;

use Params\ValidationResult;

interface Rule
{
    /**
     * @param string $name
     * @param mixed $value
     * @return ValidationResult
     * @throws \Params\Exception\ParamMissingException
     */
    public function __invoke(string $name, $value) : ValidationResult;
}
