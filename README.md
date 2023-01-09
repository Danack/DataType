# DataType

A library for validating input and creating types.

[![Actions Status](https://github.com/Danack/DataType/workflows/Tests/badge.svg)](https://github.com/Danack/DataType/actions)

# Installation

```composer require danack/datatype```

# Example usage

The full documentation is in [DOCS.md](DOCS.md), but here is an example usage.

In your controller, you would have some code to create the type. e.g. for Symfony you would have something like:

```php
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SearchController
{
    public function index(Request $request, SearchRepo $searchRepo): JsonResponse
    {
        $searchParams = SearchParams::createFromRequest($request);

        $data = $searchRepo->search($searchParams);

        return $this->json($data);
    }
}
```


In your code you would have a data type that represents a particular concept, e.g. the search parameter used in the API: 

```php

class SearchParameters implements DataType
{
    use CreateFromRequest;
    use CreateFromVarMap;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[SearchTerm('search')]
        public string $phrase,

        #[MaxItems('limit')]
        public int $limit,

        #[ArticleSearchOrdering('order')]
        public Ordering $ordering,
    ) {
    }
}
```

And each of the elements in the 'search parameters' would have their own rule based based details:

```php
<?php

use DataType\ExtractRule\GetString;
use DataType\InputType;
use DataType\HasInputType;
use DataType\ProcessRule\MaxLength;
use DataType\ProcessRule\MinLength;

#[\Attribute]
class SearchTerm implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetString(),
            new MinLength(3),
            new MaxLength(200),
        );
    }
}
```

```php
<?php

namespace DataTypeExample\InputTypes;

use DataType\InputType;
use DataType\ExtractRule\GetIntOrDefault;
use DataType\HasInputType;
use DataType\ProcessRule\MaxIntValue;
use DataType\ProcessRule\MinIntValue;

#[\Attribute]
class MaxItems implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetIntOrDefault(20),
            new MinIntValue(1),
            new MaxIntValue(200),
        );
    }
}
```


## OpenAPI descriptions

The OpenAPI/Swagger specification for individual DataTypes can be generated directly from each DataType. e.g. This code:

```php
<?php

$openapi_descriptions = generateOpenApiV300DescriptionForDataType(SearchParameters::class);

echo json_encode($openapi_descriptions, JSON_PRETTY_PRINT);
```

Would generate:

```json
[
    {
        "name": "search",
        "required": true,
        "schema": {
            "type": "string",
            "minLength": 3,
            "maxLength": 200
        }
    },
    {
        "name": "limit",
        "required": false,
        "schema": {
            "minimum": 1,
            "maximum": 200,
            "default": 20,
            "type": "integer",
            "exclusiveMaximum": false,
            "exclusiveMinimum": false
        }
    },
    {
        "name": "order",
        "required": false,
        "schema": {
            "default": "article_id",
            "type": "array"
        }
    }
]
```
which would be consumed by the front-end, either dynamically or statically through being converted to code.

To note, the OpenAPI generation is one of the less tested part of the code.

## Contributing

There are a few areas where contributions will be warmly welcomed:

- Error messages. Writing clear error messages is always hard. There are almost certainly some areas where the message could be clearer, or just more consistent with other messages. All the messages are in src/DataType/Messages.php

- Documentation. Contributions to documentation are always welcome.

- More extraction and processing rules. Although the library currently fits my needs well, it is likely there are common rules that are not currently included.

### Contributing code

We have several tools that are run to improve code quality. Please run `sh runTests.sh` to run them.

Pull requests should have full unit test coverage. Preferably also full mutation coverage through infection.




