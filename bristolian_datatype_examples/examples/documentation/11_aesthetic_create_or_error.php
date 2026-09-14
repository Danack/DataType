<?php

declare(strict_types=1);

/**
 * Aesthetic choice: createOrErrorFrom* returns [object|null, ValidationProblem[]].
 * Doc extract: Example_aesthetic_create_or_error
 */

namespace BristolianDatatypeExamples\AestheticCreateOrError;

use DataType\Basic\BasicIntegerOrDefault;
use DataType\Basic\BasicString;
use DataType\Create\CreateOrErrorFromRequest;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use Psr\Http\Message\ServerRequestInterface;

class SearchDataType implements DataType
{
    use CreateOrErrorFromRequest;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[BasicString('search')]
        public readonly string $search,
        #[BasicIntegerOrDefault('limit', 10)]
        public readonly int $limit,
    ) {
    }
}

// Example_aesthetic_create_or_error start
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
// Example_aesthetic_create_or_error end

interface SearchRepo
{
    /** @return array<mixed> */
    public function search(string $search, int $limit): array;
}
