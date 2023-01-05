<?php

declare(strict_types = 1);

namespace DataTypeExample\Parameters;

use DataType\Create\CreateFromRequest;
use DataType\Create\CreateFromVarMap;
use DataType\GetInputTypesFromAttributes;
use DataTypeExample\InputTypes\SearchTerm;
use DataTypeExample\InputTypes\MaxItems;
use DataTypeExample\InputTypes\ArticleSearchOrdering;
use DataType\Value\Ordering;
use DataType\DataType;

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