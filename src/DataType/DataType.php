<?php

declare(strict_types = 1);


namespace DataType;

/**
 * Each DataType should have a list of the individual
 * input types.
 */
interface DataType
{
    /**
     * @return InputType[]
     */
    public static function getInputTypes(): array;
}
