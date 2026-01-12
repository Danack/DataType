<?php

declare(strict_types=1);

namespace DataType\Create;

/**
 * Comprehensive factory trait that provides all error-returning creation methods.
 *
 * This trait consolidates all the individual CreateOrError* traits into a single
 * "batteries included" trait. Use this if you prefer returning errors instead of throwing exceptions.
 *
 * All methods return a tuple: [DataType|null, ValidationProblem[]]
 * - First element is the created object or null if validation failed
 * - Second element is an array of ValidationProblems (empty if successful)
 *
 * Provides methods:
 * - createOrErrorFromRequest() - from PSR-7 ServerRequest
 * - createOrErrorFromArray() - from plain array
 * - createOrErrorFromJson() - from JSON string
 * - createOrErrorFromVarMap() - from VarMap
 */
trait DataTypeFactoryOrError
{
    use CreateOrErrorFromRequest;
    use CreateOrErrorFromArray;
    use CreateOrErrorFromJson;
    use CreateOrErrorFromVarMap;
}
