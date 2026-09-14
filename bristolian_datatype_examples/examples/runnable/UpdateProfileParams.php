<?php

declare(strict_types=1);

namespace BristolianDatatypeExamples\Runnable;

use DataType\Basic\BasicString;
use DataType\Basic\OptionalBasicString;
use DataType\Create\CreateFromRequest;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;

class UpdateProfileParams implements DataType
{
    use CreateFromRequest;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[BasicString('display_name')]
        public readonly string $display_name,
        #[OptionalBasicString('bio')]
        public readonly string|null $bio,
    ) {
    }
}
