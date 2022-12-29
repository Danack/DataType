<?php

declare(strict_types = 1);

namespace SeparatingInputTypeFromStoredType;

use TypeSpec\Create\CreateFromRequest;
use TypeSpec\HasDataTypeList;
use TypeSpec\DataType;

/**
 * This represents the input data
 */
class UserCreateParams implements HasDataTypeList
{
    use ToArray;
    use CreateFromRequest;

    public function __construct(
        private string $username,
        private string $password
    ) { }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return \TypeSpec\DataType[]
     */
    public static function getDataTypeList(): array
    {
        return [
            new DataType(
                'username',
                new GetString(),
                new MinLength(4),
                new MaxLength(2048)
            ),
            new DataType(
                'password',
                new GetString(),
                new MinLength(4),
                new MaxLength(256)
            ),
        ];
    }
}
