<?php

declare(strict_types=1);

namespace DataType;

use DataType\OpenApi\ParamDescription;

/**
 * All rules (both extraction and processing) must be able to update the ParamDescription, so that an
 * OpenAPI description can be generated for all of the parameters.
 */
interface Rule
{
    public function updateParamDescription(ParamDescription $paramDescription): void;
}
