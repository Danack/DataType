<?php

namespace DataType\ExtractRule;

use DataType\ExtractRule\GetType;
use function DataType\getInputTypeListForClass;

class EasierGetType extends GetType
{
    /**
     * @param class-string $class_name
     */
    public function __construct(string $class_name)
    {
        parent::__construct(
            $class_name,
            getInputTypeListForClass($class_name)
        );
    }
}
