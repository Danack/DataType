This document details how tests should be run, and which tests are available.

# Running Tests

**Important**: Always use the provided bash scripts to run tests. Running PHPUnit directly without the correct configuration will cause tests to fail because:

Tests should be run from within the Docker container for consistency

## Available Scripts

All scripts should be run from the project root directory:

| Script | Purpose |
|--------|---------|
| `sh runUnitTests.sh` | Run PHPUnit tests with coverage |
| `sh runPhpStan.sh` | Run PHPStan static analysis (level 8) |
| `sh runPsalm.sh` | Run Psalm static analysis |
| `sh runCodeSniffer.sh` | Run PHP CodeSniffer for style checks |
| `sh runMutationTests.sh` | Run Infection mutation tests |
| `sh runExamples.sh` | Run example files |
| `sh runTests.sh` | Run all tests (unit, codesniffer, phpstan, examples, mutation) |

The scripts support passing command line options through to the underlying tools. For example:

```bash
sh runUnitTests.sh --filter GetStringTest
sh runUnitTests.sh --no-coverage
```

To start the Docker development container, run `sh runLocal.sh`.

## Running Tests in Docker

Tests run inside the `datatype-developing_8_2-1` container. To run commands manually:

```bash
docker exec datatype-developing_8_2-1 bash -c "sh runUnitTests.sh"
```

## Test Fixtures

Shared test fixtures are located in `test/fixtures.php`. This file is loaded by the PHPUnit bootstrap and contains:

- Test classes and enums used across multiple test files
- Helper functions like `createProcessedValuesFromArray()`
- Data providers like `getBoolTestWorks()` and `getBoolBadStrings()`

When adding new shared test fixtures, add them to `fixtures.php` rather than duplicating in individual test files.

## Assertion Methods

The `BaseTestCase` class provides several custom assertion methods for validation testing:

- `assertProblems($validationResult, $messagesByIdentifier)` - Regexp matching for ValidationResult
- `assertValidationProblemRegexp($identifier, $message, $problems)` - Regexp matching for problems array  
- `assertValidationProblems($identifiersAndProblems, $problems)` - Exact matching (use regexp version for templates)
- `assertNoProblems($validationResult)` - Assert no validation errors
- `assertValidationProblemContains($identifier, $needle, $problems)` - String contains matching

Use the regexp variants when testing message templates that contain `%s` placeholders.

# Finishing a piece of work

When you think a piece of work has been finished, please run all the individual code quality tools, and then run `sh runTests.sh` which is a final sanity check run.

**CODE COVERAGE SHOULD ALWAYS BE AT 100% WHEN A PIECE OF WORK IS FINISHED.**
