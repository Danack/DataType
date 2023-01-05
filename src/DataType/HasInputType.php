<?php

declare(strict_types=1);


namespace DataType;

interface HasInputType
{
    public function getInputType(): InputType;
}
