<?php

declare(strict_types = 1);

namespace DataTypeExample;

use DataType\ExtractRule\GetString;
use DataType\InputType;

class MacAddressType
{
    /** @var string */
    private $value;

    /**
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public static function getParamInfo(string $inputName): InputType
    {
        return new InputType(
            $inputName,
            new GetString(),
            new RespectMacRule()
        );
    }
}
