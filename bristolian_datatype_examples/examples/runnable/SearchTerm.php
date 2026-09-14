<?php

declare(strict_types=1);

namespace BristolianDatatypeExamples\Runnable;

use DataType\ExtractRule\GetString;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\MaxLength;
use DataType\ProcessRule\MinLength;
use DataType\ProcessRule\Trim;

#[\Attribute]
class SearchTerm implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetString(),
            new Trim(),
            new MinLength(3),
            new MaxLength(200),
        );
    }
}
