<?php

declare(strict_types = 1);

namespace DataTypeTest\DTOTypes;

use DataType\DataType;
use DataTypeTest\InputType\MultipleBasicDTO;
use DataTypeTest\InputType\Quantity;
use DataType\GetInputTypesFromAttributes;

class AdvancedDTO implements DataType
{
    use GetInputTypesFromAttributes;

    public function __construct(
        #[MultipleBasicDTO('colors')]
        public array $colors,
        #[Quantity('total')]
        public int $total,
    ) {
    }
}
