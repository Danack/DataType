<?php

namespace DataTypeTest;

use DataType\Exception\LogicExceptionData;
use DataType\OpenApi\OpenApiV300ParamDescription;
use DataType\Rule;
use DataType\ValidationResult;
use PHPUnit\Framework\TestCase;
use DataType\ProcessedValues;
use DataType\OpenApi\ParamDescription;
use Danack\PHPUnitHelper\StringTemplateMatching;
use function \Danack\PHPUnitHelper\templateStringToRegExp;

/**
 * @coversNothing
 *
 * Allows checking that no code has output characters, or left the output buffer in a bad state.
 *
 */
class BaseTestCase extends TestCase
{
    use StringTemplateMatching;

    private $startLevel = null;

    public function setUp(): void
    {
        $this->startLevel = ob_get_level();
        ob_start();
    }

    public function tearDown(): void
    {
        if ($this->startLevel === null) {
            $this->assertEquals(0, 1, "startLevel was not set, cannot complete teardown");
        }
        $contents = ob_get_contents();
        ob_end_clean();

        if ($contents === false) {
            $this->fail("Failed to get contents of buffer.");
        }

        $endLevel = ob_get_level();
        $this->assertEquals($endLevel, $this->startLevel, "Mismatched ob_start/ob_end calls....somewhere");
        $this->assertEquals(
            0,
            strlen($contents),
            "Something has directly output to the screen: [".substr($contents, 0, 500)."]"
        );
    }

    public function testPHPUnitApparentlyGetsConfused()
    {
        //Basically despite having:
        //<exclude>*/BaseTestCase.php</exclude>
        //in the phpunit.xml file it still thinks this file is a test class.
        //and then complains about it not having any tests.

        // @phpstan-ignore method.alreadyNarrowedType
        $this->assertTrue(true);
    }


    protected function assertProblems(
        ValidationResult $validationResult,
        array $messagesByIdentifier
    ) {
        $validationProblems = $validationResult->getValidationProblems();

        foreach ($messagesByIdentifier as $identifier => $message) {
            $this->assertValidationProblemRegexp($identifier, $message, $validationProblems);
        }
    }

    /**
     * @param string $identifier
     * @param string $problem
     * @param \DataType\ValidationProblem[] $validationProblems
     */
    protected function assertValidationProblem(
        string $identifier,
        string $expectedProblem,
        $validationProblems
    ) {
        $correct_identifier_incorrect_message = false;

        foreach ($validationProblems as $validationProblem) {
            if ($validationProblem->getInputStorage()->getPath() !== $identifier) {
                continue;
            }

            if ($validationProblem->getProblemMessage() === $expectedProblem) {
                // correct problem message found
                return;
            }

            // loop over all entries before failing.
            $correct_identifier_incorrect_message = true;
        }

        if ($correct_identifier_incorrect_message === true) {
            $incorrectMessageText = sprintf(
                "Validation problem for identifier '%s' found, but wrong message.\nExpected: '%s'\nActual '%s'\n",
                $identifier,
                $expectedProblem,
                $validationProblem->getProblemMessage()
            );

            $this->fail($incorrectMessageText);
        }

        // Identifier not found
        $identifiers = [];
        foreach ($validationProblems as $validationProblem) {
            $identifiers[] = $validationProblem->getInputStorage()->getPath();
        }

        $missingIndentifierText = sprintf(
            "Identifier '%s' not found in validation problems. Identifiers found are '%s'",
            $identifier,
            implode(", ", $identifiers)
        );

        $this->fail($missingIndentifierText);
    }

    public function assertOneErrorAndContainsString(
        ValidationResult $validationResult,
        string $needle
    ) {
        $validationProblems = $validationResult->getValidationProblems();
        $this->assertCount(1, $validationProblems);

        $onlyProblem = $validationProblems[0];
        $this->assertStringContainsString($needle, $onlyProblem->getProblemMessage());
    }

    /**
     * @param string $identifier
     * @param string|int|float $needle
     * @param \DataType\ValidationProblem[] $validationProblems
     */
    protected function assertValidationProblemContains(
        string $identifier,
        string|int|float $needle,
        array $validationProblems
    ) {
        foreach ($validationProblems as $validationProblem) {
            if ($validationProblem->getInputStorage()->getPath() !== $identifier) {
                continue;
            }

            if (strpos($validationProblem->getProblemMessage(), $needle) !== false) {
                // Needle was found
                return;
            }

            $message = sprintf(
                "Failed to find [%s] in validation problem text of [%s]",
                $needle,
                $validationProblem->getProblemMessage()
            );
            $this->fail($message);
        }

        // Todo - make a function
        // Identifier not found
        $pathsAsStrings = [];
        foreach ($validationProblems as $validationProblem) {
            $pathsAsStrings[] = $validationProblem->getInputStorage()->getPath();
        }
        $missingIndentifierText = sprintf(
            "Identifier '%s' not found in validation problems. Identifiers found are '%s'",
            $identifier,
            implode(", ", $pathsAsStrings)
        );

        $this->fail($missingIndentifierText);
    }
    /**
     * @param string $identifier
     * @param string $expectedProblem
     * @param \DataType\ValidationProblem[] $validationProblems
     */
    protected function assertValidationProblemRegexp(
        string $identifier,
        string $expectedProblem,
        $validationProblems
    ) {
        $expectedProblemRegexp = templateStringToRegExp($expectedProblem);

        $correct_identifier_incorrect_message = false;

        foreach ($validationProblems as $validationProblem) {
            if ($validationProblem->getInputStorage()->getPath() !== $identifier) {
                continue;
            }

            if (preg_match($expectedProblemRegexp, $validationProblem->getProblemMessage())) {
                // correct problem message found
                return;
            }
            // loop over all entries before failing.
            $correct_identifier_incorrect_message = true;
        }

        if ($correct_identifier_incorrect_message === true) {
            $incorrectMessageText = sprintf(
                "Validation problem for identifier '%s' found, but wrong message.\nExpected: '%s'\nActual '%s'\n",
                $identifier,
                $expectedProblem,
                $validationProblem->getProblemMessage()
            );

            $this->fail($incorrectMessageText);
        }

        // Todo - make a function
        // Identifier not found
        $pathsAsStrings = [];
        foreach ($validationProblems as $validationProblem) {
            $pathsAsStrings[] = $validationProblem->getInputStorage()->getPath();
        }
        $missingIndentifierText = sprintf(
            "Identifier '%s' not found in validation problems. Identifiers found are '%s'",
            $identifier,
            implode(", ", $pathsAsStrings)
        );

        $this->fail($missingIndentifierText);
    }

    /**
     * @param array<array{0:string, 1:string}> $identifiersAndProblems
     * @param \DataType\ValidationProblem[] $validationProblems
     * @return void
     */
    public function assertValidationProblems($identifiersAndProblems, $validationProblems)
    {
        foreach ($identifiersAndProblems as $identifierAndProblem) {
            $identifier = $identifierAndProblem[0];
            $problem = $identifierAndProblem[1];
            $this->assertValidationProblem($identifier, $problem, $validationProblems);
        }
    }

    public function assertNoProblems(ValidationResult $validationResult)
    {
        $validationProblems = $validationResult->getValidationProblems();
        $this->assertNoValidationProblems($validationProblems);
    }

    /**
     * @param \DataType\ValidationProblem[] $validationProblems
     */
    public function assertNoValidationProblems(array $validationProblems)
    {
        if (count($validationProblems) === 0) {
            return;
        }

        $message = "Failed asserting no validation problems. Actually found:";
        foreach ($validationProblems as $validationProblem) {
            $message .= "\n  " . $validationProblem->toString();
        }

        $this->fail($message);
    }

    public function assertNoErrors(ValidationResult $validationResult)
    {
        $validationProblems = $validationResult->getValidationProblems();

        $message = '';

        if (count($validationProblems) !== 0) {
            foreach ($validationProblems as $validationProblem) {
                $message .= $validationProblem->toString();
            }

            $this->fail("Unexpected problems: " . $message);
        }
    }

    public function assertHasValue(string|int|float|bool $expectedValue, string|int $key, ProcessedValues $processedValues)
    {
        if ($processedValues->hasValue($key) !== true) {
            $this->fail("ProcessedValues does not contain a value for [$key]");
        }

        $actualValue = $processedValues->getValue($key);

        $this->assertSame(
            $expectedValue,
            $actualValue,
            "ProcessedValues contained wrong value."
        );
    }


    public function applyRuleToDescription(Rule $rule): ParamDescription
    {
        $description = new OpenApiV300ParamDescription('John');
        $rule->updateParamDescription($description);

        return $description;
    }

    /**
     * @param int $expected_count
     * @param \DataType\ValidationProblem[] $validationProblems
     */
    public function assertValidationErrorCount(int $expected_count, array $validationProblems)
    {
        if (count($validationProblems) === $expected_count) {
            return;
        }

        $message = sprintf(
            "Incorrect number of validation problems. Was expecting %d but have %d\n",
            $expected_count,
            count($validationProblems)
        );

        $message .= "They are: \n";

        foreach ($validationProblems as $validationProblem) {
            $message .= "\t" . $validationProblem->toString() . "\n";
        }

        $this->fail($message);
    }
}
