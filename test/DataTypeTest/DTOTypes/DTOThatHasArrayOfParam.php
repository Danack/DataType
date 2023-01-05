<?php

declare(strict_types = 1);

namespace DataTypeTest\DTOTypes;

use DataTypeTest\PropertyTypes\MultipleBasicArray;
use DataType\GetInputTypesFromAttributes;

class DTOThatHasArrayOfParam
{
    use GetInputTypesFromAttributes;

    public function __construct(
        #[MultipleBasicArray('quantities')]
        public array $quantities,
        //        #[Quantity('total')]
        //        public float $total,
    ) {
    }
}
