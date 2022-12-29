<?php

declare(strict_types = 1);

namespace TypeSpecExample;

use TypeSpec\ExtractRule\GetString;
use TypeSpec\DataType;

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

    public static function getParamInfo(string $inputName): DataType
    {
        return new DataType(
            $inputName,
            new GetString(),
            new RespectMacRule()
        );
    }
}
