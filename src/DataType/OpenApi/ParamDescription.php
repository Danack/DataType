<?php

declare(strict_types=1);

namespace DataType\OpenApi;

interface ParamDescription
{
    // string Required. The name of the parameter. Parameter names are case sensitive.
    // If in is "path", the name field MUST correspond to the associated path segment from
    // the path field in the     Paths Object. See Path Templating for further information.
    //
    // For all other cases, the name corresponds to the parameter name used based on the in property.
    public function setName(string $name): void;

    // string Required. The location of the parameter. Possible values are
    // "query", "header", "path", "formData" or "body".
    public function setIn(string $in): void;

    // string A brief description of the parameter. This could contain examples
    // of use. GFM syntax can be used for rich text representation.
    public function setDescription(string $description): void;


    public function getDescription(): ?string;

    public function getFormat(): ?string;

    // boolean Determines whether this parameter is mandatory. If the parameter
    // is in "path", this property is required and its value MUST be true. Otherwise,
    // the property MAY be included and its default value is false.
    public function setRequired(bool $required): void;

    public function getRequired(): ?bool;

    // ***************
    // If in is "body"
    // ***************

    // Schema Object Required. The schema defining the type used for the body parameter.
    public function setSchema(string $schema): void;

    // Below is for in when != "body"

    //  string Required. The type of the parameter. Since the parameter is not located
    // at the request body, it is limited to simple types (that is, not an object). The
    // value MUST be one of "string", "number", "integer", "boolean", "array" or "file".
    // If type is "file", the consumes MUST be either "multipart/form-data",
    // " application/x-www-form-urlencoded" or both and the parameter MUST be in "formData".

    const TYPE_STRING = "string";
    const TYPE_NUMBER = "number";
    const TYPE_INTEGER = "integer";
    const TYPE_BOOLEAN = "boolean";
    const TYPE_ARRAY = "array";
    const TYPE_FILE = "file";

    public function setType(string $type): void;

    const FORMAT_INTEGER    = "integer"; // integerint32 signed 32 bits
    const FORMAT_LONG       = "long";    // integerint64 signed 64 bits
    const FORMAT_FLOAT      = "float";   // number float
    const FORMAT_DOUBLE     = "double";  // number double
    const FORMAT_STRING     = "string";  // string
    const FORMAT_BYTE       = "byte";    // string byte base64 encoded characters
    const FORMAT_BINARY     = "binary";  // string binary any sequence of octets
    const FORMAT_BOOLEAN    = "boolean"; // boolean
    const FORMAT_DATE       = "date";    // string date As defined by full-date - RFC3339
    const FORMAT_DATETIME   = "date-time"; // string date-time As defined by date-time - RFC3339
    const FORMAT_PASSWORD   = "password";  // string password Used to hint UIs the input needs to be obscured.
    // string The extending format for the previously mentioned type. See
    // Data Type Formats for further details.
    public function setFormat(string $format): void;

    // boolean Sets the ability to pass empty-valued parameters. This is valid
    // only for either query or formData parameters and allows you to send a
    // parameter with a name only or an empty value. Default value is false.
    public function setAllowEmptyValue(bool $allowEmptyValue): void;

    // Items Object Required if type is "array". Describes the type of items in the array.
    public function getItems() : ItemsObject;
    // TODO - how to get the $itemsObject to update this?
    public function setItems(ItemsObject $itemsObject): void;
    // TODO - how to get the $itemsObject to update this?

    // string Determines the format of the array if type array is used. Possible values are:
    // csv - comma separated values foo,bar.
    // ssv - space separated values foo bar.
    // tsv - tab separated values foo\tbar.
    // pipes - pipe separated values foo|bar.
    // multi - corresponds to multiple parameter instances instead of multiple values for a single instance
    // foo=bar&foo=baz. This is valid only for parameters in "query" or "formData".
    // Default value is csv.

    const COLLECTION_CSV = 'csv'; // - comma separated values foo,bar.
    const COLLECTION_SSV = 'ssv'; // - space separated values foo bar.
    const COLLECTION_TSV = 'tsv'; // - tab separated values foo\tbar.
    const COLLECTION_PIPES = 'pipes';  // - pipe separated values foo|bar.

    public function setCollectionFormat(string $collectionFormat): void;
    public function getCollectionFormat(): ?string;


    // * Declares the value of the parameter that the server will use if none
    // is provided, for example a "count" to control the number of results per page
    // might default to 100 if not supplied by the client in the request. (Note:
    // "default" has no meaning for required parameters.) See https://tools.ietf
    //.org/html/draft-fge-json-schema-validation-00#section-6.2. Unlike JSON
    // Schema this value MUST conform to the defined type for this parameter.
    /**
     * @param mixed $default
     */
    public function setDefault($default): void;

    // number See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.1.2.

    /**
     * @param int|float $maximum
     */
    public function setMaximum(int|float $maximum): void;

    /**
     * @return int|float|null
     */
    public function getMaximum();


    /**
     * @return int|float|null
     */
    public function getMinimum();


    // boolean See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.1.2.
    public function setExclusiveMaximum(bool $exclusiveMaximum): void;

    // number See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.1.3.
    /**
     * @param int|float $minimum
     */
    public function setMinimum($minimum): void;

    // boolean See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.1.3.
    public function setExclusiveMinimum(bool $exclusiveMinimum): void;

    // integer See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.2.1.
    public function setMaxLength(int $maxLength): void;
    public function getMaxLength(): ?int;

    // integer See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.2.2.
    public function setMinLength(int $minLength): void;
    public function getMinLength(): ?int;

    // string See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.2.3.
    public function setPattern(string $pattern): void;

    // integer See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.3.2.
    public function setMaxItems(int $maxItems): void;

    public function getMaxItems(): ?int;

    // integer See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.3.3.
    public function setMinItems(int $minItems): void;

    public function getMinItems(): ?int;

    public function setNullAllowed(bool $allowed): void;

    public function getNullAllowed(): ?bool;

    // boolean See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.3.4.
    public function setUniqueItems(bool $uniqueItems): void;

    // [*] See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.5.1.
    //    The value of this keyword MUST be an array.  This array MUST have at
    //   least one element.  Elements in the array MUST be unique.
    //
    // Elements in the array MAY be of any type, including null.
    /**
     * @param array<mixed> $enumValues
     */
    public function setEnum(array $enumValues): void;

    public function getEnum(): ?array;

    // number See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.1.1.

    /**
     * @param int|float $multiple
     */
    public function setMultipleOf($multiple): void;
}
