<?php

declare(strict_types = 1);

namespace TypeSpecExample\Parameters;

use TypeSpec\Create\CreateFromRequest;
use TypeSpec\Create\CreateFromVarMap;
use TypeSpec\GetDataTypeListFromAttributes;
use TypeSpecExample\PropertyTypes\SearchTerm;
use TypeSpecExample\PropertyTypes\MaxItems;
use TypeSpecExample\PropertyTypes\ArticleSearchOrdering;
use TypeSpec\Value\Ordering;
use TypeSpec\HasDataTypeList;

class SearchParameters implements HasDataTypeList
{
    use CreateFromRequest;
    use CreateFromVarMap;
    use GetDataTypeListFromAttributes;

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