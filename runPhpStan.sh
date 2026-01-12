#!/usr/bin/env bash

set -e
set -x

echo "Running PHPStan"
php vendor/bin/phpstan analyze -c ./phpstan.neon -l 8 src