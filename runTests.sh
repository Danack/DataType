#!/usr/bin/env bash

set -e
set -x

bash runUnitTests.sh --no-coverage

bash runCodeSniffer.sh

bash runPhpStan.sh

bash runExamples.sh

echo ""
echo "Tests completed without problem"

bash runMutationTests.sh


# rerun unit tests to get the stats again, to save scrolling...
sh runUnitTests.sh

echo "Tests completed without problem"
