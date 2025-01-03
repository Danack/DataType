<?php

declare(strict_types = 1);

namespace DataType;

/**
 * Class to hold all error messages, rather than have them distributed across the code.
 */
class Messages
{
    // Array
    public const ERROR_MESSAGE_NOT_ARRAY = "Value must be an array.";
    public const ERROR_MESSAGE_NOT_ARRAY_VARIANT_1 = "Value must be an array.";
    public const BAD_TYPE_FOR_ARRAY_ACCESS = "Cannot use type [%s] for array access";
    public const BAD_TYPE_FOR_STRING_PROCESS_RULE = "Rule %s can only process strings";

    public const ERROR_TOO_FEW_ELEMENTS = "Number of elements too small. Min allowed is %d but only got %d.";
    public const ERROR_TOO_MANY_ELEMENTS = "Number of elements too large. Max allowed is %d but got %d.";
    public const ERROR_WRONG_TYPE = "Minimum count can only be applied to an array but tried to operate on %s.";
    public const ERROR_WRONG_TYPE_VARIANT_1 = "Maximum count can only be applied to an array but tried to operate on %s.";

    // Bool
    public const ERROR_BOOL_BAD_STRING = "Boolean values must be either 'true' or 'false' but found '%s'.";

    // DateTime
    public const ERROR_INVALID_DATETIME = 'Value was not a valid RFC3339 date time apparently';
    public const ERROR_DATETIME_MUST_START_AS_STRING = 'Input for datetime must be string';
    public const ERROR_DATE_FORMAT_MUST_BE_STRING = 'Datetime format must be array of strings. Found type %s at position %d';

    public const ERROR_EMAIL_NO_AT_CHARACTER = "Email addresses must contain an '@' character.";
    public const ERROR_EMAIL_INVALID = "Not a valid email address.";

    // Enum
    // TODO - this message looks wrong.
    public const ENUM_MAP_UNRECOGNISED_VALUE_MULTIPLE = "Value [%s] is not known. Please use any of %s.";
    public const ENUM_MAP_UNRECOGNISED_VALUE_SINGLE = "Value [%s] is not known. Please use one of %s";

    // URL
    public const ERROR_INVALID_URL = 'Value was not a valid URL apparently';

    // General errors
    public const VALUE_NOT_SET = 'Value not set.';
    public const ERROR_MESSAGE_NOT_SET = "Value not set.";
    public const ERROR_MESSAGE_NOT_SET_VARIANT_1 = "Value must be set.";

    // When a input type can't be converted to a desired type
    // e.g. resource -> bool
    public const UNSUPPORTED_TYPE = "Unsupported input type of '%s'";

    // Int
    public const INT_REQUIRED_FOUND_NON_DIGITS = "Must contain only digits. Non-digit found at position %d.";
    public const INT_REQUIRED_FOUND_NON_DIGITS2 = "Integer required, must contain only digits.";
    public const INTEGER_TOO_LONG = "Value too long, max %s digits";
    public const INT_TOO_SMALL = "Value too small. Min allowed is %s";
    public const INT_TOO_LARGE = "Value too large. Max allowed is %s";
    public const INT_REQUIRED_UNSUPPORTED_TYPE = "Int or string representing an int expected, found type '%s'";
    public const INT_REQUIRED_FOUND_EMPTY_STRING = "Value is an empty string - must be an integer.";
    public const INT_OVER_LIMIT = "Value too large. Max allowed is %s";

    // Float
    public const NEED_FLOAT_NOT_EMPTY_STRING = "Value is an empty string - must be a floating point number.";
    public const FLOAT_TOO_SMALL = "Value too small. Min allowed is %s";
    public const FLOAT_TOO_LARGE = "Value too large. Max allowed is %s";
    public const FLOAT_REQUIRED = "Value must be a floating point number.";
    public const FLOAT_REQUIRED_WRONG_TYPE = "Value must be a floating point number but found '%s'";
    public const FLOAT_REQUIRED_FOUND_WHITESPACE = "Value must be floating point number, whitespace found.";

    // Order
    public const ORDER_VALUE_UNKNOWN = "Cannot order by [%s], as not known for this operation. Known are [%s]";

    // String
    public const STRING_EXPECTED = "String expected, but have type %s.";
    public const STRING_TOO_SHORT = "String too short, min characters is %d";
    public const STRING_TOO_LONG = "String too long, max characters is %d.";
    public const STRING_INVALID_COMBINING_CHARACTERS = "Invalid combining characters found at position %s";
    public const STRING_REQUIRED_FOUND_NON_SCALAR = "String expected but found non-scalar.";
    public const STRING_REQUIRED_FOUND_NULL = "String expected but found null.";
    public const STRING_REQUIRES_PREFIX = "The string must start with [%s].";
    public const STRING_FOUND_INVALID_CHAR = "Invalid character at position %d. Allowed characters are %s";

    // Miscellaneous
    public const MUST_RETURN_ARRAY_OF_PROPERTY_DEFINITION ='Function %s::getPropertyDefinitionList must return array of %s. Item at index %d is wrong type.';
    public const CLASS_NOT_FOUND = "Class %s isn't available through auto-loader.";
    public const CLASS_MUST_IMPLEMENT_DATATYPE_INTERFACE = "Class %s doesn't implement the %s interface. Cannot be used to get array of type.";
    public const INCORRECT_NUMBER_OF_PARAMETERS = "Class %s expects %d parameters but we have %d.";
    public const MISSING_PARAMETER_NAME = "Class '%s' requires a parameter named '%s', but that is missing.";
    public const PROPERTY_MULTIPLE_INPUT_TYPE_SPEC = "Property '%s' in class %s has more than one DateType annotation.";
    public const PROPERTY_ANNOTATION_DOES_NOT_EXIST = "Property '%s' in class %s has an annotation for %s, but that class does not exist.";
    public const CLASS_LACKS_CONSTRUCTOR = "Class %s has no constructor, cannot be instantiated with params";
    public const CLASS_LACKS_PUBLIC_CONSTRUCTOR = "Class %s has no public constructor, cannot be instantiated with params";
    public const NULL_NOT_ALLOWED = "null is not allowed.";

    // Rule errors
    public const ERROR_DIFFERENT_TYPES = "Parameter cannot by the same as %s as they are different types, %s and %s.";
    public const ERROR_NO_PREVIOUS_PARAMETER = "Parameter named '%s' was not previously processed.";
    public const ERROR_DIFFERENT_VALUE = "Parameter is different to parameter '%s'.";
    public const ERROR_MAXIMUM_COUNT_MINIMUM = "Maximum count must be zero or above.";
    public const ERROR_MINIMUM_COUNT_MINIMUM = "Minimum count must be zero or above.";
    public const MUST_DUPLICATE_PARAMETER = "Must be duplicate of %s";
    public const TIME_MUST_BE_X_MINUTES_AFTER_PREVIOUS_VALUE = "Time must be at least %d minutes after %s.";

    // Time
    public const MINUTES_MUST_BE_GREATER_THAN_ZERO = "minutes must be >= 0";
    public const PREVIOUS_TIME_MUST_BE_DATETIMEINTERFACE = "Previous param %s must be an object of DateTimeInterface.";
    public const CURRENT_TIME_MUST_BE_DATETIMEINTERFACE = "Current value must be of type DateTimeInterface but instead is of type %s.";
    public const TIME_MUST_BE_X_MINUTES_AFTER_TIME = "This datetime must be %s minutes after param %s which has time %s";
    public const TIME_MUST_BE_X_MINUTES_BEFORE_PARAM = "Time must be at least %d minutes before %s.";
    public const TIME_MUST_BE_X_MINUTES_BEFORE_PARAM_ERROR = "This datetime %s must be %s minutes before param %s which has time %s";

    public const TIME_MUST_BE_X_MINUTES_AFTER_PARAM_ERROR = "This datetime %s must be %s minutes after param %s which has time %s";

    public const TIME_MUST_BE_BEFORE_TIME = "This datetime must be before time %s";
    public const TIME_MUST_BE_AFTER_TIME = "This datetime must be after time %s";

    // Kernel/Matrix
    public const BAD_TYPE_FOR_KERNEL_MATRIX_PROCESS_RULE = "Kernel Matrix can only process json strings";
    public const KERNEL_MATRIX_ARRAY_EXPECTED = "Kernel Matrix, array expected but value is %s";

    public const KERNEL_MATRIX_ERROR_AT_ROW_2D_EXPECTED = "Error at row %s - 2d array expected";

    public const KERNEL_MATRIX_ERROR_AT_ROW_COLUMN_NUMBER_EXPECTED = "Row %s column %s 2d array of numbers expected";

    public const MATRIX_INVALID_BAD_ROW = "KernelMatrix must be a 2d array of floats.";
    public const MATRIX_INVALID_BAD_CELL = "KernelMatrix must be a 2d array of floats.";
    public const MATRIX_MUST_BE_ODD_SIZED_ROWS_ARE_EVEN = "Matrix must be odd-size - number of rows is even";
    public const MATRIX_MUST_BE_ODD_SIZED_COLUMNS_ARE_EVEN = "Matrix must be odd-size - number of columns is even.";
    public const MATRIX_MUST_BE_SQUARE = "Matrix must be square, but is %d x %d.";
    public const MATRIX_MUST_BE_OF_SIZE = "Matrix must be of size %d x %d, but is %d x %d.";
    public const MATRIX_MUST_BE_OF_ROW_SIZE = "Matrix must have %d rows, but has %d.";
    public const MATRIX_MUST_BE_OF_COLUMN_SIZE = "Matrix must have %d columns, but has %d.";

    // Colors
    public const BAD_COLOR_STRING = "Input [%s] does not look like a valid color string.";
}
