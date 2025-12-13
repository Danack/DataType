#!/usr/bin/env bash

set -e
set -x

bash runUnitTests.sh --no-coverage


bash runCodeSniffer.sh

echo "Running PHPStan"
php vendor/bin/phpstan analyze -c ./phpstan.neon -l 8 src

echo "Running Psalm"
# The version of psalm we are using only works on below PHP 8.3
if php -r 'exit(!(PHP_MAJOR_VERSION === 8 && (PHP_MINOR_VERSION === 1 || PHP_MINOR_VERSION === 2)));'; then
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
