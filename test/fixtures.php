<?php /** @noinspection ALL */

declare(strict_types=1);

use TypeSpec\ExtractRule\GetString;
use TypeSpec\ExtractRule\GetStringOrDefault;
use TypeSpec\HasDataTypeList;
use TypeSpec\DataType;
use TypeSpec\HasDataType;
use TypeSpec\ProcessRule\AlwaysErrorsRule;
use TypeSpec\ProcessRule\ImagickIsRgbColor;
use TypeSpec\SafeAccess;
use TypeSpecTest\ImagickColorHasDataType;
use TypeSpec\Create\CreateFromArray;
use TypeSpec\Create\CreateOrErrorFromArray;
use TypeSpec\Create\CreateFromVarMap;
use TypeSpec\GetDataTypeListFromAttributes;

class TestObject
{
    private string $name;
    private int $age;

    public function __construct(
        string $name,
        int $age
    ) {
        $this->name = $name;
        $this->age = $age;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAge(): int
    {
        return $this->age;
    }
}

class DoesNotImplementInputParameterList
{
}


class ReturnsBadHasDataTypeList implements HasDataTypeList
{
    public static function getDataTypeList(): array
    {
        return [
            // Wrong type
            new StdClass()
        ];
    }
}

class TestParams implements HasDataTypeList
{
    private string $name;

    /**
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function getDataTypeList(): array
    {
        return [
            new DataType(
                'name',
                new GetString(),
            )
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}


class AlwaysErrorsParams implements HasDataTypeList
{
    public const ERROR_MESSAGE = 'Forced error';

    public static function getDataTypeList(): array
    {
        return [
            new DataType(
                'foo',
                new GetString(),
            ),
            new DataType(
                'bar',
                new GetString(),
                new AlwaysErrorsRule(self::ERROR_MESSAGE)
            )
        ];
    }
}

class ThreeColors implements HasDataTypeList
{
    use SafeAccess;
    use CreateFromVarMap;
    use GetDataTypeListFromAttributes;

    public function __construct(
        #[ImagickColorHasDataType('rgb(225, 225, 225)', 'background_color')]
        private string $background_color,
        #[ImagickColorHasDataType('rgb(0, 0, 0)', 'stroke_color')]
        private string $stroke_color,
        #[ImagickColorHasDataType('DodgerBlue2', 'fill_color')]
        private string $fill_color
    ) {
    }

    public function getBackgroundColor(): string
    {
        return $this->background_color;
    }

    public function getStrokeColor(): string
    {
        return $this->stroke_color;
    }

    public function getFillColor(): string
    {
        return $this->fill_color;
    }
}

#[\Attribute]
class NotActuallyAParam
{
    public function __construct(
        private string $name,
        private string $default
    ) {
    }
}

class NotAParameter
{
}



class OneColor
{
    use SafeAccess;
    use CreateFromArray;

    #[ImagickColorHasDataType('rgb(225, 225, 225)', 'background_color')]
    private string $background_color;

    public function __construct(string $stroke_color, string $background_color)
    {
        $this->background_color = $background_color;
        $this->stroke_color = $stroke_color;
    }

    public function getBackgroundColor(): string
    {
        return $this->background_color;
    }
}


class TwoColors
{
    use SafeAccess;
    use CreateOrErrorFromArray;

    #[ImagickColorHasDataType('rgb(225, 225, 225)', 'background_color')]
    private string $background_color;

    #[NotActuallyAParam('fill_color', 'rgb(0, 0, 0)')]
    private string $fill_color;

    #[ImagickColorHasDataType('rgb(0, 0, 0)', 'stroke_color')]
    private string $stroke_color;

    #[NotAParameter()]
    private $non_param_property;

    /**
     * OneColor constructor.
     * @param string $background_color
     * @param string $stroke_color
     */
    public function __construct(string $stroke_color, string $background_color)
    {
        $this->background_color = $background_color;
        $this->stroke_color = $stroke_color;
    }

    public function getBackgroundColor(): string
    {
        return $this->background_color;
    }

    public function getStrokeColor(): string
    {
        return $this->stroke_color;
    }
}


class OneColorWithOtherAnnotationThatIsNotAParam
{
    use SafeAccess;

    #[ImagickColorHasDataType('rgb(225, 225, 225)', 'background_color')]
    private string $background_color;

    #[NotActuallyAParam('stroke_color', 'rgb(0, 0, 0)')]
    private string $stroke_color;

    /**
     * OneColor constructor.
     * @param string $background_color
     * @param string $stroke_color
     */
    public function __construct(string $background_color)
    {
        $this->background_color = $background_color;
    }

    public function getBackgroundColor(): string
    {
        return $this->background_color;
    }

    public function getStrokeColor(): string
    {
        return $this->stroke_color;
    }
}



class OneColorWithOtherAnnotationThatDoesNotExist
{
    use SafeAccess;

    #[ImagickColorHasDataType('rgb(225, 225, 225)', 'background_color')]
    private string $background_color;

    #[ThisClassDoesNotExistParam('stroke_color', 'rgb(0, 0, 0)')]
    private string $stroke_color;

    /**
     * OneColor constructor.
     * @param string $background_color
     * @param string $stroke_color
     */
    public function __construct(string $background_color)
    {
        $this->background_color = $background_color;
    }
}


class ThreeColorsMissingConstructorParam
{
    use SafeAccess;

    #[ImagickColorHasDataType('rgb(225, 225, 225)', 'background_color')]
    private string $background_color;

    #[ImagickColorHasDataType('rgb(0, 0, 0)', 'stroke_color')]
    private string $stroke_color;

    #[ImagickColorHasDataType('DodgerBlue2', 'fill_color')]
    private string $fill_color;

    public function __construct(string $background_color, string $stroke_color)
    {
        $this->background_color = $background_color;
        $this->stroke_color = $stroke_color;
    }
}




class ThreeColorsMissingPropertyParam
{
    use SafeAccess;

    #[ImagickColorHasDataType('rgb(225, 225, 225)', 'background_color')]
    private string $background_color;

    #[ImagickColorHasDataType('rgb(0, 0, 0)', 'stroke_color')]
    private string $stroke_color;

    private string $fill_color;

    public function __construct(string $background_color, string $stroke_color, string $fill_color)
    {
        $this->background_color = $background_color;
        $this->stroke_color = $stroke_color;
        $this->fill_color = $fill_color;
    }
}


class OneColorNoConstructor
{
    use SafeAccess;

    #[ImagickColorHasDataType('rgb(225, 225, 225)', 'background_color')]
    private string $background_color;
}

class ThreeColorsPrivateConstructor
{
    use SafeAccess;

    #[ImagickColorHasDataType('rgb(225, 225, 225)', 'background_color')]
    private string $background_color;

    /**
     * ThreeColorsPrivateConstructor constructor.
     * @param string $background_color
     */
    private function __construct(string $background_color)
    {
        $this->background_color = $background_color;
    }
}



class ThreeColorsIncorrectParamName
{
    use SafeAccess;

    #[ImagickColorHasDataType('rgb(225, 225, 225)', 'background_color')]
    private string $background_color;

    #[ImagickColorHasDataType('rgb(0, 0, 0)', 'stroke_color')]
    private string $stroke_color;

    #[ImagickColorHasDataType('rgb(0, 0, 255)', 'fill_color')]
    private string $fill_color;

    public function __construct(string $background_color, string $stroke_color, string $solid_color)
    {
        $this->background_color = $background_color;
        $this->stroke_color = $stroke_color;
        $this->fill_color = $solid_color;
    }
}



class MultipleParamAnnotations
{
    use SafeAccess;

    #[ImagickColorHasDataType('rgb(225, 225, 225)', 'background_color')]
    #[ImagickColorHasDataType('rgb(225, 225, 225)', 'fill_color')]
    private string $background_color;

    /**
     * OneColor constructor.
     * @param string $background_color
     * @param string $stroke_color
     */
    public function __construct(string $background_color)
    {
        $this->background_color = $background_color;
        $this->stroke_color = $stroke_color;
    }

    public function getBackgroundColor(): string
    {
        return $this->background_color;
    }

    public function getStrokeColor(): string
    {
        return $this->stroke_color;
    }
}

#[\Attribute]
class AttributesExistsNoConstructor
{

}

#[\Attribute]
class AttributesExistsHasConstructor
{
    public function __construct(private int $foo)
    {
    }

    /**
     * @return int
     */
    public function getFoo(): int
    {
        return $this->foo;
    }
}

#[\Attribute]
class AttributesExistsHasConstructorWithName
{
    public function __construct(private int $foo, private string $name)
    {
    }

    public function getFoo(): int
    {
        return $this->foo;
    }

    public function getName(): string
    {
        return $this->name;
    }
}


class ReflectionClassOfAttributeObject
{
    #[AttributeNotExist()]
    private $attribute_not_exists;

    #[AttributesExistsNoConstructor()]
    private $attribute_exists_no_constructor;

    #[AttributesExistsHasConstructor(10)]
    private $attribute_exists_has_constructor;

    #[AttributesExistsHasConstructorWithName(10)]
    private $attribute_exists_has_constructor_with_name;
}



class OneColorGetsCorrectSpelling
{
    use SafeAccess;
    use CreateFromArray;
    use CreateOrErrorFromArray;

    const DEFAULT_COLOR = "rgb(225, 225, 225)";

    #[ImagickColorHasDataType(self::DEFAULT_COLOR, 'backgroundColor')] //this is input name
    private string $background_color; // this is target name

    public function __construct(string $background_color)
    {
        $this->background_color = $background_color;
    }

    public function getBackgroundColor(): string
    {
        return $this->background_color;
    }
}
