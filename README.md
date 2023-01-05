# TypeSpec

A library for validating input and creating types.

[![Actions Status](https://github.com/Danack/TypeSpec/workflows/Tests/badge.svg)](https://github.com/Danack/TypeSpec/actions)

# Installation

```composer require danack/typespec```


# 

In your controller, you would have some code to create the type. e.g. for Symfony you would have something like:

```php
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SearchController
{
    public function index(Request $request): JsonResponse
    {
        $searchParams = SearchParams::createFromRequest($request);
        
        return $this->json(['username' => 'jane.doe']);
    }
}
```


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


```php



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



## Contributing

There are a few areas where contributions will be warmly welcomed:

* error messages.
* documentation.
* more extract and process rules - although the library currently fits my needs well, it is likely there are common rules that are not currently included.

### Tests

We have several tools that are run to improve code quality. Please run `sh runTests.sh` to run them all.

Pull requests should have full unit test coverage. Preferably also full mutation coverage through infection.




