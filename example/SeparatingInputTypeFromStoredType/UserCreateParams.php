<?php

declare(strict_types = 1);

namespace SeparatingInputTypeFromStoredType;

use DataType\Create\CreateFromRequest;
use DataType\DataType;
use DataType\InputType;

/**
 * This represents the input data
 */
class UserCreateParams implements DataType
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
     * @return \DataType\InputType[]
     */
    public static function getInputTypes(): array
    {
        return [
            new InputType(
                'username',
                new GetString(),
                new MinLength(4),
                new MaxLength(2048)
            ),
            new InputType(
                'password',
                new GetString(),
                new MinLength(4),
                new MaxLength(256)
            ),
        ];
    }
}
