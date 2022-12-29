<?php

declare(strict_types=1);


namespace TypeSpec;

interface HasDataType
{
    public function getDataType(): DataType;
}
