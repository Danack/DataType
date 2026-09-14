<?php

declare(strict_types=1);

namespace BristolianDatatypeExamples\Runnable;

use DataType\Basic\BasicString;
use DataType\Create\CreateOrErrorFromArray;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;

class RenameParams implements DataType
{
    use CreateOrErrorFromArray;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[BasicString('name')]
        public readonly string $name,
    ) {
    }
}
