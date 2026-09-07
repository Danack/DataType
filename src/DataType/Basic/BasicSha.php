<?php

namespace DataType\Basic;

use DataType\ExtractRule\GetString;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\ExactLength;
use DataType\ProcessRule\MatchesRegex;

/**
 * Required string input.
 */
#[\Attribute]
class BasicSha implements HasInputType
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
            new ExactLength(40), // Only accept full length SHAs
            new MatchesRegex('/\A[0-9a-f]{40}\z/i')
        );
    }
}
