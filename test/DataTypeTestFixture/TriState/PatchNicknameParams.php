<?php

declare(strict_types=1);

namespace DataTypeTestFixture\TriState;

use DataType\DataType;
use DataType\ExtractRule\GetOptionalNullableString;
use DataType\InputType;
use DataType\Presence\Absent;
use DataType\Presence\PresentNull;
use DataType\ProcessRule\MaxLength;

/**
 * PATCH-style body for testing tri-state nickname handling.
 */
class PatchNicknameParams implements DataType
{
    public function __construct(
        private Absent|PresentNull|string $nickname,
    ) {
    }

    public function getNickname(): Absent|PresentNull|string
    {
        return $this->nickname;
    }

    /**
     * @return \DataType\InputType[]
     */
    public static function getInputTypes(): array
    {
        return [
            new InputType(
                'nickname',
                new GetOptionalNullableString(),
                new MaxLength(128),
            ),
        ];
    }
}
