<?php


namespace {

    use DataType\DataStorage\ArrayDataStorage;
    use DataType\Exception\JsonDecodeException;
    use DataType\Exception\ValidationException;
    use DataType\ValidationProblem;
    use function DataType\create;
    use function DataType\getInputTypeListForClass;
    use function DataType\json_decode_safe;
    use DataType\Create\CreateFromJson;


    final class PhpStanTest
    {
        use CreateFromJson;
    }


}

namespace debugging {

    use DataType\Create\CreateFromJson;

    final class PhpStanTestInNamespace
    {
        use CreateFromJson;
    }
}
