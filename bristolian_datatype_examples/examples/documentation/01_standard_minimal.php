<?php

declare(strict_types=1);

/**
 * Opening example: SearchDataType used in a controller (shown first in docs).
 * Doc extracts: Example_search_controller_usage, Example_search_datatype
 */

namespace BristolianDatatypeExamples\Standard;

use DataType\Basic\BasicIntegerOrDefault;
use DataType\Basic\BasicString;
use DataType\Create\CreateFromRequest;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use Psr\Http\Message\ServerRequestInterface;

// Example_search_controller_usage start
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
// Example_search_controller_usage end

// Example_search_datatype start
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
// Example_search_datatype end

/** Stand-in for whatever serves search results. */
interface SearchRepo
{
    /** @return array<mixed> */
    public function search(string $search, int $limit): array;
}
