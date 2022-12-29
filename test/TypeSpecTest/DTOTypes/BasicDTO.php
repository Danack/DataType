<?php

declare(strict_types = 1);

namespace TypeSpecTest\DTOTypes;

use TypeSpecTest\PropertyTypes\KnownColors;
use TypeSpecTest\PropertyTypes\Quantity;
use TypeSpec\GetDataTypeListFromAttributes;

class BasicDTO
{
    use GetDataTypeListFromAttributes;

    public function __construct(
        #[KnownColors('color')]
        public string $color,
        #[Quantity('quantity')]
        public float $quantity,
    ) {
    }
}
