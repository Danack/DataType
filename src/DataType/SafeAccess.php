<?php

namespace DataType;

/**
 * @codeCoverageIgnore
 */
trait SafeAccess
{
    public function __set(string $name, mixed $value): void
    {
        throw new \Exception("Property [$name] doesn't exist for class [".get_class($this)."] so can't set it");
    }

    public function __get(string $name): mixed
    {
        throw new \Exception("Property [$name] doesn't exist for class [".get_class($this)."] so can't get it");
    }
}
