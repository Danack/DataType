<?php

namespace DataTypeExample\InputTypes;

use DataType\InputType;
use DataType\ExtractRule\GetStringOrDefault;
use DataType\HasInputType;
use DataType\ProcessRule\Order;

#[\Attribute]
class ArticleSearchOrdering implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetStringOrDefault('article_id'),
            new Order(['date', 'article_id'])
        );
    }
}
