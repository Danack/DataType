
# Using with frameworks

Support is built into the library for creating DataTypes from PSR7 requests, and VarMap objects. For other frameworks, please look at:

* Laravel - todo
* Symfony - https://packagist.org/packages/danack/data-type-for-symfony
* WordPress - todo

# Writing your own processing rules

TODO - write some words. For now, look at the classes in the directory 'DataType\ProcessRule\ProcessRule', and see how they are implemented.

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