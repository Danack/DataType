<?php

declare(strict_types=1);

namespace DataTypeExample;

use DataType\Create\CreateFromRequest;
use DataType\Create\CreateFromVarMap;
use DataType\Create\CreateOrErrorFromVarMap;
use DataType\ExtractRule\GetIntOrDefault;
use DataType\ExtractRule\GetStringOrDefault;
use DataType\InputType;
use DataType\SafeAccess;
use DataType\ProcessRule\MaxIntValue;
use DataType\ProcessRule\MaxLength;
use DataType\ProcessRule\MinIntValue;
use DataType\ProcessRule\Order;
use DataType\ProcessRule\SkipIfNull;
use DataType\Value\Ordering;
use DataType\DataType;

// TODO - change to type?
class GetArticlesParameters implements DataType
{
    use SafeAccess;
    use CreateFromRequest;
    use CreateFromVarMap;
    use CreateOrErrorFromVarMap;

    const LIMIT_DEFAULT = 10;

    const LIMIT_MIN = 1;
    const LIMIT_MAX = 200;

    const ARTICLE_ID_NAME = 'articleId';
    const ARTICLE_ID_INTERNAL = 'articleId';

    const ARTICLE_DATE_NAME = 'date';
    const ARTICLE_DATE_INTERNAL = 'date';

    const OFFSET_MAX = 1000000000000000;

    /** @return string[] */
    public static function getKnownOrderNames()
    {
        return [
            GetArticlesParameters::ARTICLE_ID_NAME,
            GetArticlesParameters::ARTICLE_DATE_NAME
        ];
    }

    /** @var Ordering  */
    private $ordering;

    /** @var int  */
    private $limit;

    /** @var int|null  */
    private $afterId;

    public function __construct(Ordering $ordering, int $limit, ?int $afterId)
    {
        $this->ordering = $ordering;
        $this->limit = $limit;
        $this->afterId = $afterId;
    }

    /**
     * @return \DataType\InputType[]
     */
    public static function getInputTypes(): array
    {
        return [
            new InputType(
                'ordering',
                new GetStringOrDefault('-date'),
                new Order(self::getKnownOrderNames())
            ),
            new InputType(
                'limit',
                new GetIntOrDefault(self::LIMIT_DEFAULT),
                new MinIntValue(self::LIMIT_MIN),
                new MaxIntValue(self::LIMIT_MAX)
            ),
            new InputType(
                'afterId',
                new GetStringOrDefault(null),
                new SkipIfNull(),
                new MinIntValue(0),
                new MaxIntValue(self::OFFSET_MAX)
            )
        ];
    }

    /**
     * @return Ordering
     */
    public function getOrdering(): Ordering
    {
        return $this->ordering;
    }

    /**
     * @return int|null
     */
    public function getAfterId(): ?int
    {
        return $this->afterId;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
}
