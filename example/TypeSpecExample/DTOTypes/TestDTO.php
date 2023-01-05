<?php

declare(strict_types = 1);

namespace TypeSpecExample\DTOTypes;

use TypeSpecExample\PropertyTypes\KnownColors;
use TypeSpecExample\PropertyTypes\Quantity;
use TypeSpec\GetDataTypeListFromAttributes;
use TypeSpec\HasDataTypeList;

class TestDTO implements HasDataTypeList
{
    use GetDataTypeListFromAttributes;

    public function __construct(
        #[KnownColors('color')]
        public string $color,
        #[Quantity('quantity')]
        public int $quantity,
    ) {
    }
}