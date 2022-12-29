<?php

declare(strict_types = 1);

namespace TypeSpecTest\DTOTypes;

use TypeSpecTest\PropertyTypes\MultipleBasicArray;
use TypeSpec\GetDataTypeListFromAttributes;

class DTOThatHasArrayOfParam
{
    use GetDataTypeListFromAttributes;

    public function __construct(
        #[MultipleBasicArray('quantities')]
        public array $quantities,
        //        #[Quantity('total')]
        //        public float $total,
    ) {
    }
}
