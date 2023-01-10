

# Writing your own processing rules

\DataType\ExtractRule\ExtractRule
\DataType\ProcessRule\ProcessRule


ValidationResult::valueResult
ValidationResult::finalValueResult
ValidationResult::errorResult
ValidationResult::errorButContinueResult


# OpenAPI / Swagger specification generation

You can generate OpenAPI/Swagger specifications from any DataType with code like:

```php
<!-- Example_OpenApi_generation -->
```

Which will output something like:

```json
<!-- Example_OpenApi_generation_output -->
```

The exact details of how that is passed to your front-end, or whoever is consuming the parameter specification, it outside the scope of this library.