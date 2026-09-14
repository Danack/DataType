<?php

declare(strict_types = 1);

namespace DataType\Exception\Runtime;

use DataType\Exception\DataTypeRuntimeException;

/**
 * Failure to encode json. This is probably only used
 * inside test code in the library.
 */
class JsonEncodeException extends DataTypeRuntimeException
{

}
