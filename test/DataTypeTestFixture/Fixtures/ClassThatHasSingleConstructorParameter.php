<?php

namespace DataTypeTestFixture\Fixtures;

class ClassThatHasSingleConstructorParameter
{
    public int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }
}
