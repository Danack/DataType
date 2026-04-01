#!/usr/bin/env bash

set -e
set -x

bash runUnitTests.sh --no-coverage

bash runCodeSniffer.sh

bash runPhpStan.sh

bash runExamples.sh

echo ""
echo "Tests completed without problem"

if php -r 'exit(version_compare(PHP_VERSION, "8.2.0", ">=") ? 0 : 1);'; then
    bash runMutationTests.sh
else
    echo "Skipping mutation tests because PHP is lower than 8.2"
fi


# rerun unit tests to get the stats again, to save scrolling...
sh runUnitTests.sh

echo "Tests completed without problem"
