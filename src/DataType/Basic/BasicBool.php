<?php

declare(strict_types=1);

namespace DataType\Basic;

use DataType\ExtractRule\GetBool;
use DataType\HasInputType;
use DataType\InputType;

/**
 * Required boolean input. Accepts true/false (bool) or "true"/"false" (string).
 */
#[\Attribute]
class BasicBool implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetBool(),
        );
    }
}
