<?php

declare(strict_types=1);

namespace DataType\Exception;

/**
 * The root class for all 'checked' exceptions for this library.
 *
 * Checked exceptions are the ones that can result from normal, correct use
 * of the library. For example, the ValidationException that is thrown when
 * user data is found to be invalid.
 *
 * @checked
 */
class DataTypeRuntimeException extends \Exception
{

}
