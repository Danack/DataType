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
<!-- Example_search_controller_usage -->
```

`SearchDataType` is a DataType: constructor parameters are the fields, attributes name the request keys and how they are read:

```php
<!-- Example_search_datatype -->
```

`createFromRequest` reads the PSR-7 request, validates, and returns `SearchDataType` — or throws `ValidationException`.

Built-in attributes such as `BasicString` and `BasicIntegerOrDefault` live under `DataType\Basic\`. Put domain rules (length, format, …) on your own `HasInputType` attributes.

## Aesthetic around error handling and Developer eXperience

The createFromRequest or other createFromXXX methods either returns a object that is valid (according to the InputTypes that are used in it) or throws an ValidationException (TODO - make wording more precise about what exception is returned).

```php
<!-- Example_aesthetic_create_exception -->
```

Alternatively the createOrErrorFromXXX pattern of usage can be used to avoid exceptions, and return either the DataType instance you asked to create or `null`, together with any validation problems (`array{0: T|null, 1: \DataType\ValidationProblem[]}` where `T` is the `class-string<T>` passed in).

```php
<!-- Example_aesthetic_create_or_error -->
```

Having to catch 'expected' exceptions can make a code base quite 'noisy' and having to repeat code everywhere. For myself, when invalid data is sent to a program, I want a single, standard 'formatting process' used to generate the response. e.g. programs should use a consistent set of error codes (whether they are HTTP error codes, or command line status code), and the data sent back should have a single defined layout.

So for myself, I sometimes like to use a 'injection creation' pattern. This is done through the programmable dependency injector I use. Using it looks like this:

```php
<!-- Example_using_static_factory_pattern -->
```

And configuring it looks like this:


```php
<!-- Example_configuring_static_factory_injector -->
```

It requires that the DataType class implements the `StaticFactory` interface, which  can be implemented using a trait:


```php
<!-- Example_static_factory_boring_details -->
```


---

## Creating bespoke data types for your application

DataType -> has InputTypes -> which are made of ExtractRules and Process Rules.

To make the libary easy to use, some basic types are included, but ideally you would create your own, to be specific to the api call, rather than generic.


### Basic types available from library

To make the library easier for use in prototyping, it ships with a whole load of generic basic types.


<!-- Table_basic_types -->


Ideally, you would only use these for prototyping, before creating application specific types.





...

### Creating your own application specific types




You don't actually need to use the attributes to implement the HasInputTypes interface. If you feel like it, you can wire up the DataTypes with the ExtractRules and ProcessRules yourself.


```php
<!-- Example_datatype_without_attributes -->
```


The attributes are there to make life easy, to configure DataTypes, and to re-use InputTypes across different DataTypes, but you don't _have_ to use them.





## Library exceptions

<!-- Table_exceptions -->

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
<!-- Example_laravel_controller -->
```

### Keeping a FormRequest as a thin adapter

If you still want FormRequest for auth/authorize only, validate/construct inside `passedValidation()` or the controller — do not duplicate rule lists in `$rules`.

```php
<!-- Example_laravel_form_request_adapter -->
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

