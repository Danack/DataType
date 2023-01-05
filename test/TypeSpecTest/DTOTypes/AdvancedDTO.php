<?php

declare(strict_types = 1);

namespace TypeSpecTest\DTOTypes;

use TypeSpec\HasDataTypeList;
use TypeSpecTest\PropertyTypes\MultipleBasicDTO;
use TypeSpecTest\PropertyTypes\Quantity;
use TypeSpec\GetDataTypeListFromAttributes;

class AdvancedDTO implements HasDataTypeList
{
    use GetDataTypeListFromAttributes;

    public function __construct(
        #[MultipleBasicDTO('colors')]
        public array $colors,
        #[Quantity('total')]
        public int $total,
    ) {
    }
}
