<!--
Generated README: run `php generate_readme.php` in this directory.
Edit this stub and the examples/documentation/*.php files; do not hand-edit the generated README.md.
Before editing this stub, copy it to README_stub.N.md (N starts at 0 and increments). Do not back up the generated README.md.
Runnable, PHPStan-clean examples live in examples/runnable/ and are tested from tests/.
Tables: `<!-- Table_{name} -->` is filled by `generate_table_{name}.php`.


-->

# DataType

This library aims to promote types of data to be "first class citizens" in your code.



## Example

In a controller, construct the DataType from the request, then use the properties:

```php
class SearchController
{
    /** @return array<mixed> */
    public function index(ServerRequestInterface $request, SearchRepo $searchRepo): array
    {
        $searchDataType = SearchDataType::createFromRequest($request);

        return $searchRepo->search(
            $searchDataType->search,
            $searchDataType->limit,
        );
    }
}

```

`SearchDataType` is a DataType: constructor parameters are the fields, attributes name the request keys and how they are read:

```php
class SearchDataType implements DataType
{
    use CreateFromRequest;
    use GetInputTypesFromAttributes;

    public function __construct(
        // Read the value from the "search" field and process it using the rules in BasicString
        #[BasicString('search')]
        public readonly string $search,
        // Read the value from the "limit" field and process it using the rules in BasicIntegerOrDefault (default 10)
        #[BasicIntegerOrDefault('limit', 10)]
        public readonly int $limit,
    ) {
    }
}

```

`createFromRequest` reads the PSR-7 request, validates, and returns `SearchDataType` — or throws `ValidationException`.

Built-in attributes such as `BasicString` and `BasicIntegerOrDefault` live under `DataType\Basic\`. Put domain rules (length, format, …) on your own `HasInputType` attributes.

## Aesthetic around error handling and Developer eXperience

The createFromRequest or other createFromXXX methods either returns a object that is valid (according to the InputTypes that are used in it) or throws an ValidationException (TODO - make wording more precise about what exception is returned).

```php
class SearchController
{
    /** @return array<mixed> */
    public function index(ServerRequestInterface $request, SearchRepo $searchRepo): array
    {
        try {
            $searchDataType = SearchDataType::createFromRequest($request);
        }
        catch (ValidationException $exception) {
            // TODO: handle ValidationException
            return [];
        }

        return $searchRepo->search(
            $searchDataType->search,
            $searchDataType->limit,
        );
    }
}

```

Alternatively the createOrErrorFromXXX pattern of usage can be used to avoid exceptions, and return either the DataType instance you asked to create or `null`, together with any validation problems (`array{0: T|null, 1: \DataType\ValidationProblem[]}` where `T` is the `class-string<T>` passed in).

```php
class SearchController
{
    /** @return array<mixed> */
    public function index(ServerRequestInterface $request, SearchRepo $searchRepo): array
    {
        [$searchDataType, $validationProblems] = SearchDataType::createOrErrorFromRequest($request);

        if ($searchDataType instanceof SearchDataType === false) {
            // TODO - handle errors.
        }

        return $searchRepo->search(
            $searchDataType->search,
            $searchDataType->limit,
        );
    }
}

```

Having to catch 'expected' exceptions can make a code base quite 'noisy' and having to repeat code everywhere. For myself, when invalid data is sent to a program, I want a single, standard 'formatting process' used to generate the response. e.g. programs should use a consistent set of error codes (whether they are HTTP error codes, or command line status code), and the data sent back should have a single defined layout.

So for myself, I sometimes like to use a 'injection creation' pattern. This is done through the programmable dependency injector I use. Using it looks like this:

```php
class SearchController
{
    /** @return array<mixed> */
    public function index(SearchDataType $searchDataType, SearchRepo $searchRepo): array
    {
        return $searchRepo->search(
            $searchDataType->search,
            $searchDataType->limit,
        );
    }
}

```

And configuring it looks like this:


```php
$injector = new \DI\Injector();
$injector->staticFactory(StaticFactory::class, 'createFromRequest');

```

It requires that the DataType class implements the `StaticFactory` interface, which  can be implemented using a trait:


```php
interface StaticFactory
{
    public static function createFromRequest(ServerRequestInterface $request): static;
}

class SearchDataType implements DataType, StaticFactory // LATER: highlight StaticFactory — the addition vs the earlier SearchDataType
{
    use CreateFromRequest;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[BasicString('search')]
        public readonly string $search,
        #[BasicIntegerOrDefault('limit', 10)]
        public readonly int $limit,
    ) {
    }
}

```


---

## Creating bespoke data types for your application

DataType -> has InputTypes -> which are made of ExtractRules and Process Rules.

To make the libary easy to use, some basic types are included, but ideally you would create your own, to be specific to the api call, rather than generic.


### Basic types available from library

To make the library easier for use in prototyping, it ships with a whole load of generic basic types.


| Type | Description |
| --- | --- |
| `BasicBool` | Required boolean input. Accepts true/false (bool) or "true"/"false" (string). |
| `BasicDateTime` | Required datetime input. Uses format "Y-m-d H:i:s" by default. |
| `BasicFloat` | Required float input. |
| `BasicInteger` | Required integer input. |
| `BasicIntegerOrDefault` | Integer input with a default when the parameter is missing. |
| `BasicPhpEnumTypeOrNull` | Optional backed-enum input. When the parameter is missing, the property receives null. Value must match a case of the given enum. |
| `BasicSha` | Required string input. |
| `BasicString` | Required string input. |
| `BoolOrNull` | Required parameter that may be null. When the value is present it must be a boolean; when null, the property receives null. |
| `DateTime` | Required datetime input. Accepts common ISO/RFC formats; optional constructor argument restricts to specific formats. |
| `DateTimeOrDefault` | Datetime input with a default when the parameter is missing. Optional second constructor argument restricts to specific formats. |
| `DateTimeOrNull` | Required parameter that may be null. When the value is present it must be a valid datetime; when null, the property receives null. Optional second constructor argument restricts to specific formats. |
| `FloatOrDefault` | Float input with a default when the parameter is missing. |
| `FloatOrNull` | Required parameter that may be null. When the value is present it must be a float; when null, the property receives null. |
| `Integer` | Required integer input. |
| `LatitudeFloat` | Required float input for latitude (-90 to 90 inclusive). |
| `LongitudeFloat` | Required float input for longitude (-180 to 180 inclusive). |
| `OptionalBasicString` | Optional string input. When the parameter is missing, the property receives null. |
| `OptionalBool` | Property type for an optional boolean with a configurable default. Uses DataType\ExtractRule\GetBoolOrDefault - accepts "true", "false" as string values. |
| `OptionalBoolNull` | Optional boolean that returns null when the parameter is not available. For an optional boolean with a default value, use OptionalBool instead. |
| `OptionalDateTime` | Optional datetime input. When the parameter is missing, the property receives null. Accepts common ISO/RFC formats; optional second constructor argument restricts to specific formats. |
| `OptionalFloat` | Optional float input. When the parameter is missing, the property receives null. |
| `OptionalInteger` | Optional integer input. When the parameter is missing, the property receives null. |
| `OptionalLatitudeFloat` | Optional float input for latitude (-90 to 90 inclusive). When the parameter is missing, the property receives null. |
| `OptionalLongitudeFloat` | Optional float input for longitude (-180 to 180 inclusive). When the parameter is missing, the property receives null. If $pairWithParam is not null, this parameter and that one must either both be set or both be missing. |
| `StringOrDefault` | String input with a default when the parameter is missing. |
| `StringOrNull` | Required parameter that may be null. When the value is present it must be a string; when null, the property receives null. |
| `TextString` | Required string input. Alias for BasicString with the same behaviour. |
| `Url` | Required string input validated as a URL (with scheme). Min length 12, max 2048. |


Ideally, you would only use these for prototyping, before creating application specific types.





...

### Creating your own application specific types




You don't actually need to use the attributes to implement the HasInputTypes interface. If you feel like it, you can wire up the DataTypes with the ExtractRules and ProcessRules yourself.


```php
class SearchDataType implements DataType
{
    use CreateFromRequest;

    public function __construct(
        public readonly string $search,
        public readonly int $limit,
    ) {
    }

    /**
     * @return InputType[]
     */
    public static function getInputTypes(): array
    {
        return [
            new InputType(
                'search',
                new GetString(),
                new Trim(),
                new MinLength(3),
                new MaxLength(200),
            ),
            new InputType(
                'limit',
                new GetIntOrDefault(10),
            ),
        ];
    }
}

```


The attributes are there to make life easy, to configure DataTypes, and to re-use InputTypes across different DataTypes, but you don't _have_ to use them.





## Library exceptions

- `DataTypeLogicException` — Thrown when a DataType or rule is misconfigured, or an internal invariant of this library is broken. Unchecked.
  - `AnnotationClassDoesNotExistExceptionData` — Thrown when a property references an annotation/attribute class name that does not exist or cannot be autoloaded.
  - `ClassInvalidException`
  - `DataTypeDefinitionException` — Thrown when a DataType class has an invalid static definition.
  - `DataTypeNotImplementedException` — Thrown when a class is expected to implement `DataType` but does not.
  - `IncorrectNumberOfParametersExceptionData` — Thrown when the number of resolved constructor arguments does not match the target class constructor parameter count.
  - `InvalidDatetimeFormatExceptionData` — Thrown when configured datetime format values are invalid (non-string).
  - `InvalidLocationExceptionData` — Thrown when code calls getValue DataStorage object with an invalid path.
  - `InvalidRulesExceptionData` — Thrown when a process-rule is configured or invoked with unsupported types.
  - `MissingClassExceptionData` — Thrown when a referenced class name cannot be found.
  - `MissingConstructorParameterNameExceptionData` — The object-class that the code is trying to create has a parameter for which no value is available.
  - `NoConstructorExceptionData` — The object-class that the code is trying to create has no constructor
  - `OpenApiExceptionData` — Thrown when OpenAPI schema/description generation encounters invalid state.
  - `PropertyHasMultipleInputTypeAnnotationsException` — Thrown when a single property declares more than one InputType annotation.
- `DataTypeRuntimeException` — The root class for all 'checked' exceptions for this library. Checked.
  - `JsonDecodeException` — Thrown when JSON decoding fails or yields an invalid result.
  - `JsonEncodeException` — Failure to encode json. This is probably only used inside test code in the library.
  - `ValidationException` — Thrown when user-provided input fails one or more validation rules.

That table needs polishing, which would be easier done in the actual project, not here.

/**
 * Thrown when a DataType or rule is misconfigured, or an internal invariant
 * of this library is broken.
 *
 * Examples include, reading a processed value that was never set, passing a 
 * non-ProcessedValue into ProcessedValues, or a regex PHP cannot compile. 
 * 
 * Invalid user input would throw ValidationException, not this class.
 *



## Notes for Laravel developers

DataType replaces most of what a FormRequest does for *input shape and validation*, without binding validation to the HTTP layer.

| Laravel habit | DataType equivalent |
| --- | --- |
| `FormRequest` + `$rules` | `DataType` + attribute property types |
| `$request->validated()` | `FooParams::createFromRequest($request)` |
| Manual `$request->input()` casting | ExtractRules on the property type |
| `$fails` / `$validator->errors()` | `CreateOrErrorFromRequest` → `ValidationProblem[]` |

### Controller-style (recommended shape)

Works with a PSR-7 request (Laravel can expose one; see glossary).

```php
class UpdateProfileParams implements DataType
{
    use CreateFromRequest;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[BasicString('display_name')]
        public readonly string $display_name,
        #[OptionalBasicString('bio')]
        public readonly string|null $bio,
    ) {
    }
}

class ProfileController
{
    /** @return array<string, mixed> */
    public function update(ServerRequestInterface $request): array
    {
        try {
            $params = UpdateProfileParams::createFromRequest($request);
        }
        catch (ValidationException $exception) {
            // Map $exception->getValidationProblems() to your JSON error shape.
            throw $exception;
        }

        // Use $params->display_name, $params->bio — already typed and validated.
        return ['ok' => true];
    }
}

```

### Keeping a FormRequest as a thin adapter

If you still want FormRequest for auth/authorize only, validate/construct inside `passedValidation()` or the controller — do not duplicate rule lists in `$rules`.

```php
class CreatePostParams implements DataType
{
    use CreateFromArray;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[BasicString('title')]
        public readonly string $title,
        #[BasicString('body')]
        public readonly string $body,
    ) {
    }
}

/**
 * Sketch of a Laravel FormRequest that does not duplicate validation rules.
 * authorize() stays here; shape/validation live on CreatePostParams.
 */
class CreatePostRequest /* extends \Illuminate\Foundation\Http\FormRequest */
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        // Intentionally empty: DataType owns validation.
        return [];
    }

    public function params(): CreatePostParams
    {
        // Prefer the PSR-7 path when available; array form is fine for docs:
        return CreatePostParams::createFromArray($this->all());
    }

    /** @return array<string, mixed> */
    private function all(): array
    {
        return [];
    }
}

// Controller: type-hint CreatePostRequest for auth, then $request->params().

```

---


## OpenAPI and swagger

So, one of the aims of this project was to make it easy to generated OpenAPI/Swagger documentation. I just haven't done that part of the library yet. Give me a shout if you want to talk about implementing it.


---

## Glossary

Terms of art used above that are not defined by this library:

| Term | Meaning |
| --- | --- |
| **PSR-7 request** | An object implementing `Psr\Http\Message\ServerRequestInterface` from [PSR-7](https://www.php-fig.org/psr/psr-7/) (PHP-FIG HTTP message interfaces). `CreateFromRequest` reads query/body parameters via a `Psr7VarMap`. |
| **PHP attribute** | `#[…]` metadata on declarations ([PHP attributes](https://www.php.net/manual/en/language.attributes.overview.php)). Here: property types that implement `HasInputType`. |
| **OpenAPI / Swagger** | Machine-readable HTTP API description. DataType can emit parameter schemas from a DataType’s rules (see library `DOCS.md` OpenAPI section). |
| **FormRequest** | Laravel’s request subclass that mixes authorization and validation. DataType is the validation/construction half, without Laravel coupling. |
| **VarMap** | Thin map of request/query variables (`danack/varmap`). Bristolian often passes one into `createFromVarMap`. |
| **StaticFactory** | Project interface (e.g. Bristolian’s) with `createFromRequest`; paired with an injector `staticFactory(...)` registration so DataTypes are method-injected. |
| **Injector** | Dependency injection container that resolves controller method parameters (e.g. Danack `DI\Injector` in Bristolian). |

