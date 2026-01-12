<?php

declare(strict_types=1);

namespace DataType\Create;

/**
 * Comprehensive factory trait that provides all exception-throwing creation methods.
 *
 * This trait consolidates all the individual Create* traits into a single
 * "batteries included" trait. Use this if you prefer exception-based error handling.
 *
 * Provides methods:
 * - createFromRequest() - from PSR-7 ServerRequest
 * - createFromArray() - from plain array
 * - createFromJson() - from JSON string
 * - createFromVarMap() - from VarMap
 * - createArrayOfTypeFromArray() - create array of DataType instances
 */
trait DataTypeFactory
{
    use CreateFromRequest;
    use CreateFromArray;
    use CreateFromJson;
    use CreateFromVarMap;
    use CreateArrayOfTypeFromArray;
}
