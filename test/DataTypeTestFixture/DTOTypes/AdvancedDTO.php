<?php

declare(strict_types = 1);

namespace DataTypeTestFixture\DTOTypes;

use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use DataTypeTestFixture\InputType\MultipleBasicDTO;
use DataTypeTestFixture\InputType\Quantity;
use DataTypeTestFixture\DTOTypes\BasicDTO;

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
