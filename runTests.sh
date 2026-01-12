#!/usr/bin/env bash

set -e
set -x

bash runUnitTests.sh --no-coverage


bash runCodeSniffer.sh

echo "Running PHPStan on src"
php vendor/bin/phpstan analyze -c ./phpstan.neon -l 8 src


echo "Running PHPStan on tests"
php vendor/bin/phpstan analyze -c ./phpstan.neon -l 8 src,test


echo "Running Psalm (debug enabled)"

# The next command will exit with non-zero code
set +e
php -r '
echo "PHP_MAJOR_VERSION=", PHP_MAJOR_VERSION, PHP_EOL;
echo "PHP_MINOR_VERSION=", PHP_MINOR_VERSION, PHP_EOL;

$shouldRunPsalm = (PHP_MAJOR_VERSION === 8 && (PHP_MINOR_VERSION === 1 || PHP_MINOR_VERSION === 2));
echo "Version check result: ", ($shouldRunPsalm ? "RUN" : "SKIP"), PHP_EOL;

exit($shouldRunPsalm ? 0 : 1);
'

php_exit_code=$?

set -e


echo "Version check exit code: $php_exit_code"

if [ $php_exit_code -eq 0 ]; then
    echo "Executing Psalm"
    php ./psalm.phar
else
    echo "Skipping Psalm (requires PHP 8.1 or 8.2)"
fi

bash runExamples.sh

echo ""
echo "Tests completed without problem"

# rerun unit tests to get the stats again, to save scrolling...
sh runUnitTests.sh

echo "Tests completed without problem"
