<?php

declare(strict_types=1);

namespace MultiLevelDataType;

use DataType\ExtractRule\GetString;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\MaxLength;
use DataType\ProcessRule\MinLength;
use DataType\ExtractRule\EasierGetType;

#[\Attribute]
class GetTypeParam implements HasInputType
{
    /**
     * @param string $name,
     * @param class-string $class_name
     */
    public function __construct(
        private string $name,
        private string $class_name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new EasierGetType($this->class_name)
        );
    }
}

