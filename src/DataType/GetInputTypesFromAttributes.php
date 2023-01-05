<?php

declare(strict_types = 1);

namespace DataType;

trait GetInputTypesFromAttributes
{
    // If PHP would allow traits to implement interfaces
    // this would implement \DataType\DataType

    /**
     * @return \DataType\InputType[]
     * @throws \ReflectionException
     */
    public static function getInputTypes(): array
    {
        return getInputTypesFromAnnotations(get_called_class());
    }
}
