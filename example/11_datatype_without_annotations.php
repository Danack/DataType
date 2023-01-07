<?php

declare(strict_types=1);

use DataType\Create\CreateFromRequest;
use DataType\Create\CreateFromArray;
use DataType\ExtractRule\GetIntOrDefault;
use DataType\ExtractRule\GetString;
use DataType\ExtractRule\GetStringOrDefault;
use DataType\InputType;
use DataType\ProcessRule\MaxIntValue;
use DataType\ProcessRule\MaxLength;
use DataType\ProcessRule\MinIntValue;
use DataType\ProcessRule\MinLength;
use DataType\ProcessRule\Order;
use DataType\Value\Ordering;
use DataType\DataType;

require __DIR__ . "/../vendor/autoload.php";

// Example_without_annotations start
class SearchParameters implements DataType
{
    use CreateFromRequest;
    use CreateFromArray;

    public function __construct(
        public string $phrase,
        public int $limit,
        public Ordering $ordering,
    ) {
    }

    /**
     * @return InputType[]
     */
    public static function getInputTypes(): array
    {
        $limit_input_type = new InputType(
            // Limit is both the name of the data in the API, and
            // the name of the property in the SearchParameters class.
            'limit',
            new GetIntOrDefault(20),
            new MinIntValue(1),
            new MaxIntValue(200),
        );

        $search_input_type = new InputType(
            // This is the name that is used in the API
            'search',
            new GetString(),
            new MinLength(3),
            new MaxLength(200),
        );

        // Set the target name for the constructor. So the API expects
        // "search", but the internal class uses "phrase".
        $search_input_type->setTargetParameterName('phrase');

        $order_input_type = new InputType(
            'order',
            new GetStringOrDefault('article_id'),
            new Order(['date', 'article_id'])
        );
        $order_input_type->setTargetParameterName('ordering');

        return [
            $limit_input_type,
            $search_input_type,
            $order_input_type
        ];
    }
}

// Example_without_annotations end

$varMap = [
    'search' => "John",
    'limit' => 20,
    'order' => "+date"
];

$searchParams = SearchParameters::createFromArray($varMap);

printf("Search for '%s' with a limit of '%d' items.\n", $searchParams->phrase, $searchParams->limit);
printf("Order by:\n");

foreach ($searchParams->ordering->getOrderElements() as $orderElement) {
    printf("  %s : %s\n",$orderElement->getName(), $orderElement->getOrder());
}

echo "\nExample behaved as expected.\n";

echo "\n";
exit(0);
