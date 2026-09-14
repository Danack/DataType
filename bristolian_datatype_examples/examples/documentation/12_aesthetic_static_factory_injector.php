<?php

declare(strict_types=1);

/**
 * Aesthetic choice: StaticFactory + injector builds the DataType for the action.
 * Doc extracts: Example_using_static_factory_pattern, Example_configuring_static_factory_injector, Example_static_factory_boring_details
 *
 * Modelled on Bristolian: implements StaticFactory, injector->staticFactory(..., 'createFromRequest').
 */

namespace BristolianDatatypeExamples\AestheticInjector;

use DataType\Basic\BasicIntegerOrDefault;
use DataType\Basic\BasicString;
use DataType\Create\CreateFromRequest;
use DataType\DataType;
use DataType\GetInputTypesFromAttributes;
use Psr\Http\Message\ServerRequestInterface;

// Example_static_factory_boring_details start
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
// Example_static_factory_boring_details end

// Example_using_static_factory_pattern start
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
// Example_using_static_factory_pattern end

// Example_configuring_static_factory_injector start
$injector = new \DI\Injector();
$injector->staticFactory(StaticFactory::class, 'createFromRequest');
// Example_configuring_static_factory_injector end

interface SearchRepo
{
    /** @return array<mixed> */
    public function search(string $search, int $limit): array;
}
