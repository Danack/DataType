<?php

declare(strict_types=1);

/**
 * Aesthetic choice: createFrom* throws ValidationException.
 * Doc extract: Example_aesthetic_create_exception
 */

namespace BristolianDatatypeExamples\Aesthetic;

use DataType\Basic\BasicIntegerOrDefault;
use DataType\Basic\BasicString;
use DataType\Create\CreateFromRequest;
use DataType\DataType;
use DataType\Exception\Runtime\ValidationException;
use DataType\GetInputTypesFromAttributes;
use Psr\Http\Message\ServerRequestInterface;

class SearchDataType implements DataType
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

// Example_aesthetic_create_exception start
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
// Example_aesthetic_create_exception end

interface SearchRepo
{
    /** @return array<mixed> */
    public function search(string $search, int $limit): array;
}
