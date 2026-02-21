<?php

declare(strict_types = 1);

namespace DataTypeTest\DTOTypes;

use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataTypeTest\InputType\MultipleBasicDTO;
use DataTypeTest\InputType\Quantity;

class AdvancedDTO implements DataType
{
    use GetInputTypesFromAttributes;

    /**
     * @param array<int, BasicDTO> $colors
     */
    public function __construct(
        #[MultipleBasicDTO('colors')]
        public array $colors,
        #[Quantity('total')]
        public int $total,
    ) {
    }
}
