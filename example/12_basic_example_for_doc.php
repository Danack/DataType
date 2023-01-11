<?php

declare(strict_types=1);

use DataType\DataStorage\ArrayDataStorage;
use DataType\DataType;
use DataType\ExtractRule\GetIntOrDefault;
use DataType\ExtractRule\GetString;
use DataType\GetInputTypesFromAttributes;
use DataType\HasInputType;
use DataType\InputType;
use DataType\ProcessRule\MaxIntValue;
use DataType\ProcessRule\MaxLength;
use DataType\ProcessRule\MinIntValue;
use DataType\ProcessRule\MinLength;
use VarMap\ArrayVarMap;
use VarMap\VarMap;
use VarMap\VarMap as Request;
use function DataType\create;
use function DataType\createOrError;
use function DataType\getInputTypeListForClass;

require __DIR__ . "/../vendor/autoload.php";


// This is some shenigans to make the code appear easier
// to understand in the doc.
trait CreateFromRequest
{
    /**
     * @param VarMap $variableMap
     * @return self
     * @throws \DataType\Exception\ValidationException
     */
    public static function createFromRequest(VarMap $variableMap)
    {
        $inputTypeList = getInputTypeListForClass(self::class);
        $dataStorage = ArrayDataStorage::fromArray($variableMap->toArray());
        $object = create(static::class, $inputTypeList, $dataStorage);
        /** @var $object self */
        return $object;
    }
}


/**
 * Creates a DataType from a VarMap.
 *
 * Returns two values, the DataType created or null and an array of ValidationProblems if there were any.
 */
trait CreateOrErrorFromRequest
{
    /**
     * @param VarMap $variableMap
     * @return array{0:?object, 1:\DataType\ValidationProblem[]}
     * @throws \DataType\Exception\ValidationException
     */
    public static function createOrErrorFromRequest(VarMap $variableMap)
    {
        $inputTypeList = getInputTypeListForClass(self::class);
        $dataStorage = ArrayDataStorage::fromArray($variableMap->toArray());

        return createOrError(static::class, $inputTypeList, $dataStorage);
    }
}




// Example_basic_usage start
/**
 * This class defines the 'username' type, so that it can be used as
 * an attribute in the GreetingParameters DataType.
 */
#[\Attribute]
class Username implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetString(),
            new MinLength(2),
            new MaxLength(200),
        );
    }
}

/**
 * This class defines the 'excitement' type, so that it can be used as
 * an attribute in the GreetingParameters DataType.
 */
#[\Attribute]
class Excitement implements HasInputType
{
    public function __construct(
        private string $name
    ) {
    }

    public function getInputType(): InputType
    {
        return new InputType(
            $this->name,
            new GetIntOrDefault(2),
            new MinIntValue(0),
            new MaxIntValue(200),
        );
    }
}


/**
 * This is the class that defines our customer DataType. It has two properties,
 * each of which was just defined.
 */
class GreetingParameters implements DataType
{
    use CreateFromRequest;
    use CreateOrErrorFromRequest;
    use GetInputTypesFromAttributes;

    public function __construct(
        #[Username('name')]
        public string $subject,

        #[Excitement('excitement')]
        public int $excitement,
    ) {
    }
}

/**
 * This is the class that uses the GreetingParameters
 */
class GreetingController
{
    public function index(Request $request)
    {
        $greeting_data = GreetingParameters::createFromRequest($request);

        $message = sprintf(
            "Greeting there %s %s.",
            $greeting_data->subject,
            str_repeat("!", $greeting_data->excitement)
        );

        echo $message;
    }
}
// Example_basic_usage end



// Example_basic_usage_no_exception start
/**
 * This is the class that uses the GreetingParameters
 */
class GreetingControllerNoException
{
    public function index(Request $request)
    {
        [$greeting_data, $validationProblems] = GreetingParameters::createOrErrorFromRequest($request);

        if (count($validationProblems) !== 0) {
            echo "Ooh, something went wrong!\n";
            foreach ($validationProblems as $validationProblem) {
                echo $validationProblem->toString() . "\n";
            }
            return;
        }

        $message = sprintf(
            "Greeting there %s %s.",
            $greeting_data->subject,
            str_repeat("!", $greeting_data->excitement)
        );

        echo $message;
    }
}
// Example_basic_usage_no_exception end



$varMap = new ArrayVarMap([
    'name' => 'John',
    'excitement' => 5,
]);

$controller = new GreetingController();
$controller->index($varMap);

echo "\n";

$errorVarMap = new ArrayVarMap([
    'naem' => 'John',
    'excitement' => 5,
]);

$controller = new GreetingControllerNoException();
$controller->index($errorVarMap);
