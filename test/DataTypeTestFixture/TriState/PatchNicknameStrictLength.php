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
 * Same tri-state nickname shape as {@see PatchNicknameParams}, but with a short
 * {@see MaxLength} so tests can assert that the “present string” path still runs
 * process rules (e.g. validation fails when the string is too long).
 */
class PatchNicknameStrictLength implements DataType
{
    public function __construct(
        /** Tri-state: omit key, explicit null, or a string value when present. */
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
                // Tri-state extract: Absent / PresentNull skip further rules; only a real string is length-checked.
                new GetOptionalNullableString(),
                // Deliberately small so values like "abcd" fail MaxLength in tests.
                new MaxLength(3),
            ),
        ];
    }
}
