<?php

namespace TypeSpecExample\PropertyTypes;

use TypeSpec\ExtractRule\GetString;
use TypeSpec\DataType;
use TypeSpec\ExtractRule\GetStringOrDefault;
use TypeSpec\HasDataType;
use TypeSpec\ProcessRule\Order;

#[\Attribute]
class ArticleSearchOrdering implements HasDataType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getDataType(): DataType
    {
        return new DataType(
            $this->name,
            new GetStringOrDefault('article_id'),
            new Order(['date', 'article_id'])
        );
    }
}
