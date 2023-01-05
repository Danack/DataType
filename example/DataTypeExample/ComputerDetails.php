<?php

declare(strict_types=1);

namespace DataTypeExample;

use DataType\Create\CreateOrErrorFromArray;
use DataType\ExtractRule\GetString;
use DataType\DataType;
use DataType\ProcessRule\MinLength;
use DataType\ProcessRule\MaxLength;
use DataType\SafeAccess;
use DataType\InputType;
use DataTypeExample\MacAddressType;

class ComputerDetails implements DataType
{
    use SafeAccess;
    use CreateOrErrorFromArray;

    /** @var string */
    private $name;

    /** @var string */
    private $macAddress;

    /**
     *
     * @param string $name
     * @param string $macAddress
     */
    public function __construct(string $name, string $macAddress)
    {
        $this->name = $name;
        $this->macAddress = $macAddress;
    }

    /**
     * @return \DataType\InputType[]
     */
    public static function getInputTypes(): array
    {
        return [
            new InputType(
                'name',
                new GetString(),
                new MinLength(2),
                new MaxLength(1024)
            ),

            MacAddressType::getParamInfo('macAddress'),
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getMacAddress(): string
    {
        return $this->macAddress;
    }
}
