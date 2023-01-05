<?php

declare(strict_types=1);

namespace DataTypeTest\Integration;

use DataType\DataType;
use DataType\SafeAccess;
use DataType\ExtractRule\GetString;
use DataType\ProcessRule\MinLength;
use DataType\ProcessRule\MaxLength;
use DataType\Create\CreateOrErrorFromVarMap;
use DataType\ProcessRule\DuplicatesParam;
use DataType\InputType;

class PasswordDoubleCheck implements DataType
{
    use SafeAccess;
    use CreateOrErrorFromVarMap;

    /** @var string  */
    private $password;

    /** @var string */
    private $password_repeat;

    public function __construct(string $password, string $password_repeat)
    {
        $this->password = $password;
        $this->password_repeat = $password_repeat;
    }

    public static function getInputTypes(): array
    {
        return [
            new InputType(
                'password',
                new GetString(),
                new MinLength(6),
                new MaxLength(60)
            ),
            new InputType(
                'password_repeat',
                new GetString(),
                new DuplicatesParam('password')
            ),
        ];
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getPasswordRepeat(): string
    {
        return $this->password_repeat;
    }
}
