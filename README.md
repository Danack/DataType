# TypeSpec

A library for validating input and creating types.

[![Actions Status](https://github.com/Danack/TypeSpec/workflows/Tests/badge.svg)](https://github.com/Danack/TypeSpec/actions)

# Installation

```composer require danack/typespec```


# TL:DR - Using in an application





Symfony
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





## Contributing

There are a few areas where contributions will be warmly welcomed:

* error messages.
* documentation.
* more extract and process rules - although the library currently fits my needs well, it is likely there are common rules that are not currently included.

### Tests

We have several tools that are run to improve code quality. Please run `sh runTests.sh` to run them all.

Pull requests should have full unit test coverage. Preferably also full mutation coverage through infection.




